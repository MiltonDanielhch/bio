<?php

namespace App\Traits;

use Illuminate\Http\Request;

trait ManagesCrud
{
    /**
     * Muestra la vista principal del listado (browse).
     */
    public function index()
    {
        $this->authorize('viewAny', $this->model);
        return view($this->browseView);
    }

    /**
     * Devuelve la lista de registros para AJAX.
     */
    public function list(Request $request)
    {
        $this->authorize('viewAny', $this->model);

        $search = $request->get('search', '');
        $paginate = $request->get('paginate', 10);

        $query = ($this->model)::query();

        // Aplicar relaciones Eager Loading si se definen en el controlador
        if (property_exists($this, 'with') && !empty($this->with)) {
            $query->with($this->with);
        }

        // Aplicar lógica de búsqueda si se define en el controlador
        if ($search && method_exists($this, 'applySearch')) {
            $this->applySearch($query, $search);
        }

        $items = $query->orderBy('id', 'desc')->paginate($paginate);

        // El nombre de la variable en la vista debe ser consistente, ej. 'items'
        return view($this->listView, ['items' => $items]);
    }
}
