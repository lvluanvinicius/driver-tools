<?php

declare (strict_types = 1);

namespace App\Services;

use CURLFile;
use Exception;

class LaravelApiClass
{
    /**
     * URL base da API
     */
    private string $baseUrl;

    /**
     * Cabeçalhos HTTP para as requisições
     */
    private array $headers;

    /**
     * Opções adicionais para as requisições cURL
     */
    private array $curlOptions;

    /**
     * Timeout para as requisições em segundos
     */
    private int $timeout;

    /**
     * Código de status HTTP da última requisição
     */
    private ?int $lastStatusCode = null;

    /**
     * Construtor da classe
     *
     * @param string $baseUrl URL base da API Laravel
     * @param string|null $token Token de autenticação (opcional)
     * @param int $timeout Timeout para as requisições em segundos
     */
    public function __construct(string $baseUrl, ?string $token = null, int $timeout = 30)
    {
        $this->baseUrl = rtrim($baseUrl, '/');
        $this->timeout = $timeout;

        // Configuração padrão de cabeçalhos
        $this->headers = [
            'Accept'       => 'application/json',
            'Content-Type' => 'application/json',
        ];

        // Configuração padrão de opções cURL
        $this->curlOptions = [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING       => '',
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_TIMEOUT        => $this->timeout,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
        ];

        // Define o token de autenticação se fornecido
        if ($token !== null) {
            $this->setToken($token);
        }
    }

    /**
     * Define o token de autenticação para as requisições
     *
     * @param string $token Token de autenticação
     * @param string $tokenType Tipo do token (padrão: Bearer)
     * @return self
     */
    public function setToken(string $token, string $tokenType = 'Bearer'): self
    {
        $this->headers['Authorization'] = "{$tokenType} {$token}";
        return $this;
    }

    /**
     * Define um cabeçalho personalizado para as requisições
     *
     * @param string $key Nome do cabeçalho
     * @param string $value Valor do cabeçalho
     * @return self
     */
    public function setHeader(string $key, string $value): self
    {
        $this->headers[$key] = $value;
        return $this;
    }

    /**
     * Define múltiplos cabeçalhos personalizados para as requisições
     *
     * @param array $headers Array associativo de cabeçalhos
     * @return self
     */
    public function setHeaders(array $headers): self
    {
        foreach ($headers as $key => $value) {
            $this->headers[$key] = $value;
        }
        return $this;
    }

    /**
     * Define uma opção cURL personalizada
     *
     * @param int $option Opção cURL (constante CURLOPT_*)
     * @param mixed $value Valor da opção
     * @return self
     */
    public function setCurlOption(int $option, mixed $value): self
    {
        $this->curlOptions[$option] = $value;
        return $this;
    }

    /**
     * Define o timeout para as requisições
     *
     * @param int $seconds Timeout em segundos
     * @return self
     */
    public function setTimeout(int $seconds): self
    {
        $this->timeout                      = $seconds;
        $this->curlOptions[CURLOPT_TIMEOUT] = $seconds;
        return $this;
    }

    /**
     * Obtém o código de status HTTP da última requisição
     *
     * @return int|null Código de status HTTP ou null se nenhuma requisição foi realizada
     */
    public function getStatusCode(): ?int
    {
        return $this->lastStatusCode;
    }

    /**
     * Constrói a URL completa para a requisição
     *
     * @param string $endpoint Endpoint da API
     * @return string URL completa
     */
    private function buildUrl(string $endpoint): string
    {
        return $this->baseUrl . '/' . ltrim($endpoint, '/');
    }

    /**
     * Prepara os cabeçalhos para o formato esperado pelo cURL
     *
     * @return array Cabeçalhos formatados para cURL
     */
    private function prepareHeaders(): array
    {
        $headers = [];
        foreach ($this->headers as $key => $value) {
            $headers[] = "{$key}: {$value}";
        }
        return $headers;
    }

    /**
     * Executa uma requisição HTTP
     *
     * @param string $method Método HTTP (GET, POST, PUT, PATCH, DELETE)
     * @param string $endpoint Endpoint da API
     * @param array|null $data Dados a serem enviados no corpo da requisição
     * @param array|null $queryParams Parâmetros da query string
     * @return array Resposta da API em formato de array associativo
     * @throws Exception Se ocorrer um erro na requisição
     */
    private function request(string $method, string $endpoint, ?array $data = null, ?array $queryParams = null): array
    {
        // Prepara a URL com query params se fornecidos
        $url = $this->buildUrl($endpoint);
        if ($queryParams !== null && count($queryParams) > 0) {
            $url .= '?' . http_build_query($queryParams);
        }

        // Inicializa cURL
        $curl = curl_init();

        // Configura as opções básicas do cURL
        curl_setopt_array($curl, $this->curlOptions);

        // Define a URL
        curl_setopt($curl, CURLOPT_URL, $url);

        // Define o método HTTP
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);

        // Define os cabeçalhos
        curl_setopt($curl, CURLOPT_HTTPHEADER, $this->prepareHeaders());

        // Adiciona os dados ao corpo da requisição se necessário
        if ($data !== null && in_array($method, ['POST', 'PUT', 'PATCH', 'DELETE'])) {
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
        }

        // Executa a requisição
        $response   = curl_exec($curl);
        $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $error      = curl_error($curl);
        $errno      = curl_errno($curl);

        // Armazena o código de status HTTP
        $this->lastStatusCode = $statusCode;

        // Fecha a conexão cURL
        curl_close($curl);

        // Verifica se ocorreu algum erro na requisição
        if ($errno) {
            throw new Exception("Erro cURL ({$errno}): {$error}");
        }

        // Decodifica a resposta JSON
        $responseData = json_decode($response, true);

        // Verifica se a decodificação JSON foi bem-sucedida
        if ($responseData === null && json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception("Erro ao decodificar resposta JSON: " . json_last_error_msg() . "\nResposta: {$response}");
        }

        // Verifica se a resposta contém um código de erro HTTP
        if ($statusCode >= 400) {
            $errorMessage = $responseData['message'] ?? 'Erro desconhecido';

            // Tratamento específico para erros comuns do Laravel
            if ($statusCode === 422 && isset($responseData['errors'])) {
                $validationErrors = json_encode($responseData['errors'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
                // throw new Exception("Erro de validação (422): {$errorMessage}\nErros: {$validationErrors}");
                // throw new Exception("{\"message\": {$errorMessage}},\n\"erros\":{$validationErrors}");
                return (array) json_decode($validationErrors);
            }

            throw new Exception($errorMessage);
        }

        return $responseData;
    }

    /**
     * Realiza uma requisição GET
     *
     * @param string $endpoint Endpoint da API
     * @param array|null $queryParams Parâmetros da query string
     * @return array Resposta da API em formato de array associativo
     */
    public function get(string $endpoint, ?array $queryParams = null): array
    {
        return $this->request('GET', $endpoint, null, $queryParams);
    }

    /**
     * Realiza uma requisição POST
     *
     * @param string $endpoint Endpoint da API
     * @param array|null $data Dados a serem enviados no corpo da requisição
     * @return array Resposta da API em formato de array associativo
     */
    public function post(string $endpoint, ?array $data = null): array
    {
        return $this->request('POST', $endpoint, $data);
    }

    /**
     * Realiza uma requisição PUT
     *
     * @param string $endpoint Endpoint da API
     * @param array|null $data Dados a serem enviados no corpo da requisição
     * @return array Resposta da API em formato de array associativo
     */
    public function put(string $endpoint, ?array $data = null): array
    {
        return $this->request('PUT', $endpoint, $data);
    }

    /**
     * Realiza uma requisição PATCH
     *
     * @param string $endpoint Endpoint da API
     * @param array|null $data Dados a serem enviados no corpo da requisição
     * @return array Resposta da API em formato de array associativo
     */
    public function patch(string $endpoint, ?array $data = null): array
    {
        return $this->request('PATCH', $endpoint, $data);
    }

    /**
     * Realiza uma requisição DELETE
     *
     * @param string $endpoint Endpoint da API
     * @param array|null $data Dados a serem enviados no corpo da requisição
     * @return array Resposta da API em formato de array associativo
     */
    public function delete(string $endpoint, ?array $data = null): array
    {
        return $this->request('DELETE', $endpoint, $data);
    }

    /**
     * Realiza upload de arquivo para a API
     *
     * @param string $endpoint Endpoint da API
     * @param string $filePath Caminho do arquivo a ser enviado
     * @param string $fileParamName Nome do parâmetro para o arquivo
     * @param array|null $additionalData Dados adicionais a serem enviados
     * @return array Resposta da API em formato de array associativo
     * @throws Exception Se o arquivo não existir ou não puder ser lido
     */
    public function uploadFile(string $endpoint, string $filePath, string $fileParamName = 'file', ?array $additionalData = null): array
    {
        // Verifica se o arquivo existe
        if (! file_exists($filePath) || ! is_readable($filePath)) {
            throw new Exception("Arquivo não encontrado ou não pode ser lido: {$filePath}");
        }

        // Cria uma cópia dos cabeçalhos atuais
        $originalHeaders = $this->headers;

        // Remove o cabeçalho Content-Type para que o cURL defina automaticamente com o boundary correto
        unset($this->headers['Content-Type']);

        // Prepara os dados do formulário
        $postFields = [];

        // Adiciona o arquivo
        $postFields[$fileParamName] = new CURLFile($filePath);

        // Adiciona dados adicionais se fornecidos
        if ($additionalData !== null) {
            foreach ($additionalData as $key => $value) {
                $postFields[$key] = $value;
            }
        }

        // Inicializa cURL
        $curl = curl_init();

        // Configura as opções básicas do cURL
        curl_setopt_array($curl, $this->curlOptions);

        // Define a URL
        curl_setopt($curl, CURLOPT_URL, $this->buildUrl($endpoint));

        // Define o método HTTP como POST
        curl_setopt($curl, CURLOPT_POST, true);

        // Define os cabeçalhos
        curl_setopt($curl, CURLOPT_HTTPHEADER, $this->prepareHeaders());

        // Define os dados do formulário
        curl_setopt($curl, CURLOPT_POSTFIELDS, $postFields);

        // Executa a requisição
        $response   = curl_exec($curl);
        $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $error      = curl_error($curl);
        $errno      = curl_errno($curl);

        // Armazena o código de status HTTP
        $this->lastStatusCode = $statusCode;

        // Fecha a conexão cURL
        curl_close($curl);

        // Restaura os cabeçalhos originais
        $this->headers = $originalHeaders;

        // Verifica se ocorreu algum erro na requisição
        if ($errno) {
            throw new Exception("Erro cURL ({$errno}): {$error}");
        }

        // Decodifica a resposta JSON
        $responseData = json_decode($response, true);

        // Verifica se a decodificação JSON foi bem-sucedida
        if ($responseData === null && json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception("Erro ao decodificar resposta JSON: " . json_last_error_msg() . "\nResposta: {$response}");
        }

        // Verifica se a resposta contém um código de erro HTTP
        if ($statusCode >= 400) {
            $errorMessage = $responseData['message'] ?? 'Erro desconhecido';
            throw new Exception("Erro HTTP {$statusCode}: {$errorMessage}");
        }

        return $responseData;
    }
}
