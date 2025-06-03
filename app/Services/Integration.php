<?php
namespace App\Services;

class Integration extends LaravelApiClass
{
    public function __construct()
    {
        parent::__construct(config("gc-integration.appurl"));

        # Configura o token de aplicação.
        $this->setHeader('App-Secret', config("gc-integration.appsecret"));
    }

    /**
     * Efetua o login dentro da api de integrações.
     * @author Luan Santos <lvluansantos@gmail.com>
     *
     * @param string $username
     * @param string $password
     * @return array
     */
    public function signIn(string $username, string $password): array
    {
        $response = $this->post('/api/app/sign-in', ['username' => $username, 'password' => $password]);

        if ($this->getStatusCode() == 422) {
            return [
                'status_code' => $this->getStatusCode(),
                'error'       => 'Por favor, verifique todos os campos.',
                'errors'      => $response,
            ];
        }

        return [
            'status_code' => $this->getStatusCode(),
            'response'    => $response,
            'error'       => null,
        ];
    }

    /**
     * Valida se a sessão está ativa.
     * @author Luan Santos <lvluansantos@gmail.com>
     *
     * @param string $userToken
     * @return bool
     */
    public function auther(string $userToken): bool
    {
        try {
            $this->setHeader('Authorization', "Bearer {$userToken}");
            $response = $this->get('/api/app/auther');

            if ($this->getStatusCode() === 200 && isset($response['status'])) {
                if ($response['status']) {
                    return true;
                }
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Recupera os dados do usuário na sessão.
     * @author Luan Santos <lvluansantos@gmail.com>
     *
     * @param string $userToken
     * @throws \Exception
     * @return array
     */
    public function session(string $userToken): array
    {
        try {
            $this->setHeader('Authorization', "Bearer {$userToken}");
            $response = $this->get('/api/app/session');

            if ($this->getStatusCode() === 200 && isset($response['status'])) {
                if ($response['status'] && isset($response['data'])) {
                    $response['status_code'] = $this->getStatusCode();
                    $response['error']       = null;
                    $response['errors']      = null;
                    return $response['data'];
                }
            }

            throw new \Exception('Sua sessão é inválida.');
        } catch (\Exception $e) {
            return [
                'status_code' => $this->getStatusCode(),
                'error'       => $e->getMessage(),
                'errors'      => null,
            ];
        }
    }

    /**
     * Efetua o logout na API.
     * @author Luan Santos <lvluansantos@gmail.com>
     *
     * @param string $userToken
     * @return array{error: null, response: array, status_code: int|null|array{error: string, errors: array, status_code: int|null}}
     */
    public function signOut(string $userToken): array
    {
        $this->setHeader('Authorization', "Bearer {$userToken}");
        $response = $this->post('/api/app/sign-out');

        return [
            'status_code' => $this->getStatusCode(),
            'response'    => $response,
            'error'       => null,
        ];
    }

    /**
     * Atualiza o perfil de um usuário.
     * @author Luan Santos <lvluansantos@gmail.com>
     *
     * @param string $userToken
     * @param string $userId
     * @param array $data
     * @throws \Exception
     * @return array
     */
    public function updateProfile(string $userToken, array $data): array
    {
        try {
            $this->setHeader('Authorization', "Bearer {$userToken}");
            $response = $this->put("/api/app/profile", $data);

            if ($this->getStatusCode() === 200 && isset($response['status'])) {
                if ($response['status'] && isset($response['data'])) {
                    $response['status_code'] = $this->getStatusCode();
                    $response['error']       = null;
                    $response['errors']      = null;
                    return $response;
                }
            }

            throw new \Exception('Houve um erro durante sua solicitação. Por favor, tente novamente mais tarde.');
        } catch (\Exception $e) {
            return [
                'status_code' => $this->getStatusCode(),
                'error'       => $e->getMessage(),
                'errors'      => null,
            ];
        }
    }

    /**
     * Recupera os registros de arquivos e pastas.
     * @author Luan Santos <lvluansantos@gmail.com>
     *
     * @param string $userToken
     * @param string|null $uuid
     * @param array $params
     * @return array
     * @throws \Exception
     */
    public function files(string $userToken, string | null $uuid = null, array $params = []): array
    {
        try {
            $this->setHeader('Authorization', "Bearer {$userToken}");
            $response = $this->get("/api/cednet-drive/files/$uuid", $params);

            if ($this->getStatusCode() === 200 && isset($response['status']) && $response['status']) {
                return $response['data'];
            }

            if (isset($response['message'])) {
                throw new \Exception($response['message']);
            }

            throw new \Exception('Houve um erro desconhecido ao tentar completar sua requisição.');

        } catch (\Exception $e) {
            return [
                'status_code' => $this->getStatusCode(),
                'error'       => $e->getMessage(),
                'errors'      => null,
            ];
        }
    }

    public function getFolder(string $userToken, string | null $uuid = null): array
    {
        try {
            $this->setHeader('Authorization', "Bearer {$userToken}");
            $response = $this->get("/api/cednet-drive/file/$uuid");

            if ($this->getStatusCode() === 200 && isset($response['status']) && $response['status']) {
                return [
                    'data'   => $response['data'],
                    'status' => true,
                ];
            }

            if (isset($response['message'])) {
                throw new \Exception($response['message']);
            }

            throw new \Exception('Houve um erro desconhecido ao tentar completar sua requisição.');

        } catch (\Exception $e) {
            return [
                'status_code' => $this->getStatusCode(),
                'error'       => $e->getMessage(),
                'errors'      => null,
            ];
        }
    }

    /**
     * Cria um novo registro de pasta.
     * @author Luan Santos <lvluansantos@gmail.com>
     *
     * @param string $userToken
     * @param string|null $uuid
     * @param array $data
     * @throws \Exception
     * @return array|array{error: string, errors: null, status_code: int|null}
     */
    public function fileCreate(string $userToken, string | null $uuid, array $data)
    {
        try {
            $this->setHeader('Authorization', "Bearer {$userToken}");
            $response = $this->post("/api/cednet-drive/files/$uuid", $data);

            if ($this->getStatusCode() == 422) {
                return [
                    'status_code' => $this->getStatusCode(),
                    'error'       => 'Por favor, verifique todos os campos.',
                    'errors'      => $response,
                ];
            }

            if ($this->getStatusCode() === 200 && isset($response['status'])) {
                if ($response['status']) {
                    $response['status_code'] = $this->getStatusCode();
                    $response['error']       = null;
                    $response['errors']      = null;
                    return $response;
                } else {
                    throw new \Exception($response['message']);
                }

            }

            throw new \Exception('Houve um erro durante sua solicitação. Por favor, tente novamente mais tarde.');

        } catch (\Exception $e) {
            return [
                'status_code' => $this->getStatusCode(),
                'error'       => $e->getMessage(),
                'errors'      => null,
            ];
        }
    }

    /**
     * Atualiza um registro de arquivo ou pasta na api.
     * @author Luan Santos <lvluansantos@gmail.com>
     *
     * @param string $userToken
     * @param string|null $uuid
     * @param string|null $parent
     * @param array $data
     * @throws \Exception
     * @return array|array{error: string, errors: null, status_code: int|null}
     */
    public function fileUpdate(string $userToken, string | null $uuid, string | null $parent = null, array $data)
    {
        try {
            $this->setHeader('Authorization', "Bearer {$userToken}");
            $response = $this->put("/api/cednet-drive/files/$uuid/$parent", $data);

            if ($this->getStatusCode() === 200 && isset($response['status'])) {
                if ($response['status']) {
                    $response['status_code'] = $this->getStatusCode();
                    $response['error']       = null;
                    $response['errors']      = null;
                    return $response;
                } else {
                    throw new \Exception($response['message']);
                }

            }

            throw new \Exception('Houve um erro durante sua solicitação. Por favor, tente novamente mais tarde.');

        } catch (\Exception $e) {
            return [
                'status_code' => $this->getStatusCode(),
                'error'       => $e->getMessage(),
                'errors'      => null,
            ];
        }
    }

    /**
     * Exclui um registro de pasta ou arquivo na api.
     * @author Luan Santos <lvluansantos@gmail.com>
     *
     * @param string $userToken
     * @param string|null $uuid
     * @param string|null $parent
     * @throws \Exception
     * @return array|array{error: string, errors: null, status_code: int|null}
     */
    public function fileDelete(string $userToken, string | null $uuid, string | null $parent = null)
    {
        try {
            $this->setHeader('Authorization', "Bearer {$userToken}");
            $response = $this->delete("/api/cednet-drive/files/$uuid/$parent");

            if ($this->getStatusCode() === 200 && isset($response['status'])) {
                if ($response['status']) {
                    $response['status_code'] = $this->getStatusCode();
                    $response['error']       = null;
                    $response['errors']      = null;
                    return $response;
                } else {
                    throw new \Exception($response['message']);
                }

            }

            throw new \Exception('Houve um erro durante sua solicitação. Por favor, tente novamente mais tarde.');

        } catch (\Exception $e) {
            return [
                'status_code' => $this->getStatusCode(),
                'error'       => $e->getMessage(),
                'errors'      => null,
            ];
        }
    }
}
