<?php

namespace MEkramy\Support;

use Exception;
use BadMethodCallException;
use Illuminate\Support\Str;
use MEkramy\PHPUtil\Helpers;
use MEkramy\OOPUtil\CanChained;
use MEkramy\OOPUtil\MapGetterSetter;

/**
 * Advanced Collection paginator
 *
 * @author m ekramy <m@ekramy.ir>
 * @access public
 * @version 1.0.0
 */
class Paginator implements \Illuminate\Contracts\Support\Jsonable, \Illuminate\Contracts\Support\Arrayable
{
    use MapGetterSetter, CanChained;

    /**
     * Request instance
     *
     * @var \Illuminate\Http\Request
     */
    protected $request;

    /**
     * Query builder instance
     *
     * @var mixed
     */
    protected $query;

    /**
     * Allowed sorts array
     *
     * @var array
     */
    protected $sorts = [];

    /**
     * Allowed limits array
     *
     * @var array
     */
    protected $limits = [10, 25, 50, 100];

    /**
     * Default pagination meta
     *
     * @var array
     */
    protected $meta = [
        'page' => 1,
        'limit' => 10,
        'sort' => 'id',
        'order' => 'asc',
        'search' => ''
    ];

    /**
     * Pagination tags
     *
     * @var array
     */
    protected $tags = [];

    /**
     * Create a new Paginator instance.
     *
     * @param \Illuminate\Http\Request $request
     */
    public function __construct(\Illuminate\Http\Request $request)
    {
        $this->request = $request;
    }

    /**
     * Resolve call with Meta and Tag postfix
     *
     * @param string $name
     * @param array $arguments
     * @throws BadMethodCallException               called method not exists and not have Meta postfix
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        if (Str::endsWith($name, "Tag")) {
            return $this->getTag(Str::replaceLast("Tag", "", $name));
        }
        if (Str::endsWith($name, "Meta")) {
            return $this->getMeta(Str::replaceLast("Meta", "", $name));
        }
        throw new BadMethodCallException("{$name} method not defined!");
    }

    /**
     * Resolve undefined property get
     *
     * @param string $name
     * @return mixed
     */
    protected function __onGetFailed(string $name)
    {
        if (Str::endsWith($name, "Tag")) {
            return $this->getTag(Str::replaceLast("Tag", "", $name));
        } else {
            return $this->getMeta(Str::replaceLast("Meta", "", $name));
        }
    }

    /**
     * Call when setter not defined
     *
     * @param string $name
     * @param mixed $value
     * @return void
     */
    protected function __onSetFailed($name, $value): void
    {
        if (Str::endsWith($name, "Tag")) {
            $this->addTag(Str::replaceLast("Tag", "", $name), $value);
        } else {
            $this->addMeta(Str::replaceLast("Meta", "", $name), $value);
        }
    }

    /**
     * Methods list to exclude in chaining call
     *
     * @return array
     */
    protected function __cantChain(): array
    {
        return ['getQuery', 'getSorts', 'getLimits', 'getPage', 'getLimit', 'getSort', 'getOrder', 'getSearch', 'getMeta', 'getTag', 'hasTag', 'toArray', 'toJson'];
    }

    /**
     * Parse query parameter from request
     *
     * @return void
     */
    public function parse(): void
    {
        $this->setPage((int) Helpers::validateNumberOrDefault($this->request->page, false, 1, null, null, 1));
        $this->setLimit((int) Helpers::validateNumberOrDefault($this->request->limit, false, 1, null, $this->limits, $this->limits[0] ?? 0));
        $this->setSort(Helpers::validateOrDefault($this->request->sort, null, $this->meta['sort']));
        $this->setOrder(Helpers::validateOrDefault($this->request->order, null, $this->meta['order']));
        $this->setSearch(Helpers::validateOrDefault($this->request->search, null, ''));
        try {
            $this->tags = json_decode(base64_decode($this->request->tags), true);
        } catch (Exception $e) {
            $this->tags = [];
        }
    }

    /**
     * Set query
     *
     * @param mixed $query
     * @return void
     */
    public function setQuery($query)
    {
        $this->query = $query;
    }

    /**
     * Get query
     *
     * @return mixed
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * Set allowed sorts
     *
     * @param array $sorts
     * @return void
     */
    public function setSorts(array $sorts)
    {
        $this->sorts = $sorts;
    }

    /**
     * Get allowed sorts
     *
     * @return array
     */
    public function getSorts(): array
    {
        return $this->sorts;
    }

    /**
     * Set allowed limits
     *
     * @param array $sorts
     * @return void
     */
    public function setLimits(array $limits)
    {
        $this->limits = $limits;
    }

    /**
     * Get allowed limits
     *
     * @return array
     */
    public function getLimits(): array
    {
        return $this->limits;
    }

    /**
     * Set page
     *
     * @param int $page
     * @return void
     */
    public function setPage(int $page)
    {
        $this->meta['page'] = $page;
    }

    /**
     * Get page
     *
     * @return int
     */
    public function getPage(): int
    {
        return $this->meta['page'];
    }

    /**
     * Set limit
     *
     * @param int $limit
     * @return void
     */
    public function setLimit(int $limit)
    {
        if (in_array($limit, $this->limits)) {
            $this->meta['limit'] = $limit;
        }
    }

    /**
     * Get limit
     *
     * @return int
     */
    public function getLimit(): int
    {
        return $this->meta['limit'];
    }

    /**
     * Set sort
     *
     * @param int $sort
     * @return void
     */
    public function setSort(int $sort)
    {
        if (in_array($sort, $this->sorts)) {
            $this->meta['sort'] = $sort;
        }
    }

    /**
     * Get sort
     *
     * @return int
     */
    public function getSort(): int
    {
        return $this->meta['sort'];
    }

    /**
     * Set order
     *
     * @param int $order
     * @return void
     */
    public function setOrder(int $order)
    {
        if (in_array($order, ['asc', 'desc'])) {
            $this->meta['order'] = $order;
        }
    }

    /**
     * Get order
     *
     * @return int
     */
    public function getOrder(): int
    {
        return $this->meta['order'];
    }

    /**
     * Set search
     *
     * @param string $search
     * @return void
     */
    public function setSearch(?string $search)
    {
        $this->meta['search'] = $search ?? '';
    }

    /**
     * Get search
     *
     * @return string
     */
    public function getSearch(): string
    {
        return $this->meta['search'];
    }

    /**
     * Add new meta
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function addMeta(string $key, $value)
    {
        $this->meta[Str::snake($key)] = $value;
    }

    /**
     * Get meta
     *
     * @param string $key
     * @return mixed
     */
    public function getMeta(string $key)
    {
        return $this->meta[Str::snake($key)] ?? null;
    }

    /**
     * Add new tag
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function addTag(string $key, $value)
    {
        $this->tags[Str::snake($key)] = $value;
    }

    /**
     * Get tag
     *
     * @param string $key
     * @return mixed
     */
    public function getTag(string $key)
    {
        return $this->tags[Str::snake($key)] ?? null;
    }

    /**
     * Check if tag exist
     *
     * @param string $key
     * @return bool
     */
    public function hasTag($key): bool
    {
        return array_key_exists(Str::snake($key), $this->tags);
    }

    /**
     * Get array of paginated collection
     *
     * @throws BadMethodCallException               query object not valid
     * @return array
     */
    public function toArray(): array
    {
        if (!method_exists($this->query, "count") || !method_exists($this->query, "paginate")) {
            throw new BadMethodCallException("Query is required and must has `count` and `paginate` methods!");
        }

        // Fix page number if current page number is greater than last page
        $lastPage = max((int) ceil($this->query->count() / $this->getLimit()), 1);
        $page = min($this->getPage(), $lastPage);
        \Illuminate\Pagination\Paginator::currentPageResolver(function () use ($page) {
            return $page;
        });
        $this->setPage($page);

        $paginatedResult = $this->query->paginate($this->getLimit())->toArray();

        // Return result
        return [
            'meta' => array_merge($this->meta, ['tags' => base64_encode(json_encode($this->tags))]),
            'pagination' => [
                'current_page' => $paginatedResult['current_page'],
                'from' => $paginatedResult['from'],
                'last_page' => $paginatedResult['last_page'],
                'per_page' => $paginatedResult['per_page'],
                'to' => $paginatedResult['to'],
                'total' => $paginatedResult['total'],
            ],
            'data' => $paginatedResult['data']
        ];
    }

    /**
     * Get json string of paginated collection
     */
    public function toJson($options = 0): string
    {
        return json_encode($this->toArray(), $options);
    }
}
