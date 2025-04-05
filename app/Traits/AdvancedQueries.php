<?php
namespace App\Traits;

use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

trait AdvancedQueries
{
    /**
     * Transforma uma string de campos em um array, renomeando os campos com o prefixo da tabela.
     *
     * @param string $attr
     * @param string $key
     * @return array
     */
    protected function parseFields(string $attr, string $key): array
    {
        return array_filter(
            array_map(
                fn($field) => $field !== '' ? [$field => "{$key}_{$field}"] : null,
                explode(',', $attr)
            )
        );
    }

    /**
     * Valida e transforma os campos de relacionamento em um formato estruturado.
     *
     * @param array $attr
     * @param array $accepts
     * @return array
     * @throws Exception
     */
    protected function parseRelationshipFields(array $attr, array $accepts): array
    {
        if (! array_key_exists('relationship_fields', $attr)) {
            return [];
        }

        $auxTransform = [];

        foreach ($attr['relationship_fields'] as $key => $value) {
            if (! in_array($key, $accepts)) {
                throw new Exception("Parâmetro $key inválido em relationship_fields.");
            }
            $auxTransform[] = [$key => $this->parseFields($value, $key)];
        }

        return $auxTransform;
    }

    /**
     * Separa os nomes das colunas originais, renomeadas e marcadas.
     *
     * @param array $attr
     * @return array
     */
    protected function parseRelationshipFieldsRenameds(array $attr): array
    {
        $fields = [
            "original" => [],
            "renamed"  => [],
            "marked"   => [],
        ];

        foreach ($attr as $tableValues) {
            foreach ($tableValues as $tableKey => $values) {
                foreach ($values as $val) {
                    foreach ($val as $keyF => $field) {
                        $fields['original'][] = $keyF;
                        $fields['renamed'][]  = $field;
                        $fields['marked'][]   = "$tableKey.$keyF";
                    }
                }
            }
        }

        return $fields;
    }

    /**
     * Cria os selects customizados para a query.
     *
     * @param array $relationsValues
     * @param array $attr
     * @param array $options
     * @return array
     * @throws Exception
     */
    protected function parseSelects(array $relationsValues, array $attr = [], array $options = []): array
    {
        $selects = [];

        foreach ($relationsValues as $relations) {
            foreach ($relations as $relationKey => $fields) {
                foreach ($fields as $field) {
                    [$cl, $as] = [array_keys($field)[0], array_values($field)[0]];
                    $selects[] = "$relationKey.$cl as $as";
                }
            }
        }

        if (array_key_exists('fields', $attr)) {
            if (empty($options)) {
                throw new Exception('Se for informado os campos da tabela principal, é necessário passar as opções obrigatórias.');
            }

            if (! in_array('table', array_keys($options))) {
                throw new Exception('Relacionamento mal informado. Necessário informar os options com a chave table.');
            }

            $fields = array_filter(explode(',', $attr['fields']), fn($field) => $field !== '');
            return array_merge($selects, $fields);
        }

        return $selects;
    }

    /**
     * Aplica filtros dinâmicos à query.
     *
     * @param Builder $query
     * @param array $filters
     * @return void
     * @throws Exception
     */
    protected function applyFilters(Builder $query, array $filters): void
    {
        foreach ($filters as $filter) {
            if (! isset($filter['field'])) {
                throw new Exception("Campo ou valor faltando nos filtros.");
            }

            if (! array_key_exists('value', $filter)) {
                throw new Exception("Campo ou valor faltando nos filtros.");
            }

            $field = $filter['field'];
            $value = $filter['value'];
            $op    = $filter['operator'] ?? '=';

            if ($op === 'in') {
                $query->whereIn($field, (array) $value);
            } elseif ($op === 'contain') {
                $query->where($field, 'LIKE', "%$value%");
            } else {
                $query->where($field, $op, $value);
            }
        }
    }

    /**
     * Aplica uma busca global à query.
     *
     * @param Builder $query
     * @param string $search
     * @param array $fields
     * @return void
     */
    protected function applyGlobalSearch(Builder $query, string $search, array $fields): void
    {
        $query->where(function ($q) use ($search, $fields) {
            foreach ($fields as $field) {
                $q->orWhere($field, 'LIKE', $search);
            }
        });
    }

    /**
     * Efetua uma busca avançada com base nos filtros e configurações de relacionamento.
     *
     * @param Builder $query
     * @param array $attr
     * @param array $options
     * @return Collection|LengthAwarePaginator
     * @throws Exception
     */
    public function advancedQuery(Builder $query, array $attr, array $options): Collection | LengthAwarePaginator
    {
        DB::enableQueryLog();

        // Relacionamentos
        if (array_key_exists('relationship_fields', $attr)) {
            if (! in_array('relations', array_keys($options)) || ! in_array('joins', array_keys($options))) {
                throw new Exception('Relacionamento mal informado. Necessário a opção relations e joins.');
            }

            $relationshipFields = $this->parseRelationshipFields($attr, $options['relations']);
            $selects            = $this->parseSelects($relationshipFields, $attr, $options);
            $query->select($selects);

            foreach ($options['joins'] as $tableKey => $tableValues) {
                if (! isset($tableValues['fk'], $tableValues['compare'])) {
                    throw new Exception('Array de Joins mal informados. Necessário passar as chaves fk e compare.');
                }

                $joinType = $tableValues['join_type'] ?? 'join';
                $query->$joinType($tableKey, "$tableKey.{$tableValues['fk']}", "=", $tableValues['compare']);
            }
        } else {
            if (! isset($attr['fields'])) {
                throw new Exception("Campos mal informados. Necessário passar ao menos um campo para retornar na consulta.");
            }
            $selects = $this->parseSelects([], $attr, $options);
            $query->select($selects);
        }

        // Filtros Dinâmicos
        if (array_key_exists('filters', $attr) && ! empty($attr['filters']) && is_array($attr['filters'])) {
            $this->applyFilters($query, $attr['filters']);
        }

        // Busca Global
        if (! empty($attr['search'])) {
            $search = '%' . strtolower($attr['search']) . '%';
            $fields = array_key_exists('fields', $attr) ? explode(',', $attr['fields']) : [];

            if (array_key_exists('relationship_fields', $attr)) {
                $relationshipFields = $this->parseRelationshipFields($attr, $options['relations']);
                $parseFields        = $this->parseRelationshipFieldsRenameds($relationshipFields);
                $fields             = array_merge($fields, $parseFields['marked']);
            }

            $this->applyGlobalSearch($query, $search, $fields);
        }

        // Ordenação
        if (array_key_exists('order_by', $attr)) {
            $order = $attr['order'] ?? 'desc';
            $query->orderBy($attr['order_by'], $order);
        }

        // Paginação
        if (array_key_exists('paginate', $attr) && ! empty($attr['paginate']) && is_numeric($attr['paginate'])) {
            return $query->paginate(intval($attr['paginate']));
        }

        return $query->get();
    }
}
