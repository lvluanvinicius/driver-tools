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
}
