<?php

namespace MEkramy\Support;

use MEkramy\PHPUtil\Helpers;

/**
 * Register macros for modules
 *
 * @author m ekramy <m@ekramy.ir>
 * @access public
 * @version 1.0.0
 */
class Macros
{
    /**
     * List of methods with required class
     *
     * @var array
     */
    private $macros = [
        'errorResponse' => 'Illuminate\Routing\ResponseFactory',
        'successResponse' => 'Illuminate\Routing\ResponseFactory',
        'unauthorizedResponse' => 'Illuminate\Routing\ResponseFactory',
        'forbiddenResponse' => 'Illuminate\Routing\ResponseFactory',
        'selectAsQuery' => 'Illuminate\Database\Query\Builder',
    ];

    /**
     * Register all macros if related class available
     *
     * @return void
     */
    public function register(): void
    {
        foreach ($this->macros as $func => $class) {
            if (class_exists($class)) {
                $this->{$func}($class);
            }
        }
    }


    /**
     * Create macro for error response
     *
     * @param \Illuminate\Routing\ResponseFactory $class
     */
    private function errorResponse($class)
    {
        call_user_func("$class::macro", 'error', function ($data = 'invalid', $code = 400) {
            return $this->json([
                'success' => false,
                'data' => $data
            ], $code);
        });
    }

    /**
     * Create macro for success response
     *
     * @param Illuminate\Routing\ResponseFactory $class
     */
    protected function successResponse($class)
    {
        call_user_func("$class::macro", 'success', function ($data = true, $code = 200) {
            return $this->json([
                'success' => true,
                'data' => $data
            ], $code);
        });
    }
    /**
     * Create macro for unauthorized response
     *
     * @param Illuminate\Routing\ResponseFactory $class
     */
    protected function unauthorizedResponse($class)
    {
        call_user_func("$class::macro", 'unauthorized', function ($data = 'unauthorized') {
            return $this->json([
                'success' => false,
                'data' => $data
            ], 401);
        });
    }

    /**
     * Create macro for forbidden response
     *
     * @param Illuminate\Routing\ResponseFactory $class
     */
    protected function forbiddenResponse($class)
    {
        call_user_func("$class::macro", 'forbidden', function ($data = 'forbidden') {
            return $this->json([
                'success' => false,
                'data' => $data
            ], 403);
        });
    }

    /**
     * Create macro for select as query
     *
     * @param Illuminate\Database\Query\Builder $class
     */
    protected function selectAsQuery($class)
    {
        call_user_func("$class::macro", 'selectAs', function ($as, $query) {
            return ($this)->selectRaw(Helpers::formatString("({query}) AS {as}", [
                'query' => trim(preg_replace('/\s+/', ' ', $query)),
                'as' => $as
            ]));
        });
    }
}
