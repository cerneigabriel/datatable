<?php

namespace Modules\DataTable\Core\Traits;

use Illuminate\Auth\Access\Response;

/**
 * Trait DataTableAuthorizer
 *
 * @package Modules\DataTable\Core\Traits
 */
trait HasAuthorizer
{
    /**
     * @return mixed
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function handleAuthorizer()
    {
        if (method_exists($this::class, 'authorize')) {
            $allowed = $this->authorize();

            if (is_bool($allowed)) {
                return (new Response($allowed, !$allowed ?  : ''))->authorize();
            }

            if ($allowed instanceof Response) {
                return $allowed->authorize();
            }
        }
        return (new Response(true))->authorize();
    }
}