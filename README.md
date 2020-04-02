
# Support Library

Support Library is a **Set of helpers for laravel**

## Publish Translations File

```bash
php artisan vendor:publish --provider="MEkramy\Support\SupportServiceProvider"
```

# Macros

## JSON API Responses

```php
response()->error($data = 'invalid', $code = 400); // HTTP status code is 400
/*
* {
*     "success" => false,
*     "data" => "invalid"
* }
*/

response()->success($data = true, $code = 200); // HTTP status code is 200
/*
* {
*     "success" => true,
*     "data" => true
* }
*/

response()->unauthorized($data = 'unauthorized'); // HTTP status code is 401
/*
* {
*     "success" => false,
*     "data" => "unauthorized"
* }
*/

response()->forbidden($data = 'forbidden'); // HTTP status code is 403
/*
* {
*     "success" => false,
*     "data" => "forbidden"
* }
*/
```

## Query Builder selectAs

```php
$user = User::where("is_banned", false)->select('phone', 'mobile')->selectAs("full_name", "CONCAT_WS('-', firstname, lastname)");
```

# Paginator

Paginator extends default laravel paginator and return json or array instance of result.

**Note:** Setting sorts list and query and calling parse method is required!

**Note:** All meta parsed from request by default but you can overwrite them.

**Note:** Tags received and sended back in base64 format to can store in url or query string

```php

use MEkramy\Support\Paginator;
use Illuminate\Routing\Controller;
class UserController extends Controller
{
    public function index(Paginator $paginator)
    {
        // Set valid sorts
        // if invalid sort passed by request first sort used
        $paginator->setSorts(['id', 'username', 'firstname', 'lastname']);
        // or
        $paginator->sorts = ['id', 'username', 'firstname', 'lastname'];

        //  Set valid limits, default is [10, 25, 50, 100]
        // if invalid limit passed by request first limit used
        $paginator->setLimits([25, 50, 100]);
        // or
        $paginator->limits = [25, 50, 100];

        // Parse page, limit, sort, order, search and tags from request
        $paginator->parse();

        // Set default meta (page, limit, sort, order, search and tags)
        $paginator->setPage((int) $request->page);
        // or
        $paginator->limit = 10;

        // Get default meta
        $sort = $paginator->sort;
        // or
        $order = $paginator->getOrder();

        // Set extra meta
        $paginator->addMeta('author', 'm ekramy');
        // or simply add Meta to end of tag name and set by property
        $paginator->authorMeta = 'm ekramy';

        // Get extra meta
        $author = $paginator->getMeta('author');
        // or simply add Meta to end of tag name and get by property
        $author = $paginator->authorMeta;

        // Set query
        $paginator->setQuery(User::where('is_banned', false));
        // you can access query by query property or getter methods
        $paginator->query->where('firstname', 'like', "%{$paginator->search}%")

        // Tags: tags are list of filters come in base64 encoded string and parsed as array

        // Check if tag passed
        // in this example accesses is array of access list ex: ["admin", "support"]
        if($paginator->hasTag('accesses')){
            $paginator->query->whereIn('access', $paginator->getTag('access'))
        }

        // Access tag
        $bannedOnly = $paginator->getTag('banned');
        // or simply add Tag to end of tag name and get by property
        $bannedOnly = $paginator->bannedTag;

        // Add Tag to final response and send to user
        $paginator->addTag('gender', 'male');
        // or simply add Tag to end of tag name and set by property
        $paginator->genderTag = 'male';

        // Get paginated result

        $arrayOfResult = $paginator->toArray();
        $jsonOfResult = $paginator->toJson();

        // or you can simply return paginator as result (return json)
        return $paginator;
        /* returned response
         * {
         *     "meta": {
         *         "page": 1,
         *         "limit": 10,
         *         "sort": "id",
         *         "order": "asc",
         *         "search": "",
         *         "author": "m ekramy",
         *         "tags": "eyJnZW5kZXIiOiJtYWxlIiwiYWNjZXNzZXMiOlsiYWRtaW4iLCJzdXBwb3J0Il19"
         *     },
         *     "pagination": {
         *         "current_page": 1,
         *         "from": 1,
         *         "last_page": 11,
         *         "per_page": 10,
         *         "to": 10,
         *         "total": 103
         *     },
         *     "data": {...}
         * }
    }
}
```

# Rate Limiter

Rate Limiter extends default laravel RateLimiter and allow to use rate limiter as object.

```php

use MEkramy\Support\Paginator;
use Illuminate\Routing\Controller;

class LoginController extends Controller
{
    public function index(RateLimiter $limiter)
    {
        // Set rate limiter parameters
        // $limiter->init(string $key, int $maxAttempts: 3, int $decaySeconds: 300);
        $limiter->init('key', 5, 600);
        // or you can set parameter separately using setter or property
        $limiter->setKey('key');
        $limiter->key = 'key'
        $limiter->setMaxAttempts(5);
        $limiter->maxAttempts = 5
        $limiter->setDecaySeconds(600);
        $limiter->decaySeconds = 600;

        // Check if limiter must look
        if($limiter->mustLock()){
            return response()->error('too many attempts', 419);
            // or return number of seconds until unlock
            return response()->error($limiter->availableIn(), 419);
        }


        // increase number of try. also return current number of attempts.
        $totalAttempts = $limiter->addAttempts();

        // reset rate limiter
        $limiter->reset;

        // get number of retries left
        $retriesLeft = $limiter->retriesLeft();

        // get number of second until available to unlock
        $av = $limiter->availableIn();

        // Lock rate limiter in special case
        if($someThingHappen) {
            $limiter->lock();
        }
    }
}
```

# Validators

**username** input must only contains 0-9, a-z, A-Z, -(dash), .(dot) and _(underline).

**Note** x presents number.

**tel** match (0xx) xxxx-xxxx or 0xxxxxxxxxx

**mobile** match (09xx) xxx-xxxx or 09xxxxxxxxx

**postalcode** match xxxxx-xxxxx or xxxxxxxxxx

**identifer** match any numeric value greater then 0

**maxlength** check max length of input (input parsed to string).

```php
    "username" => "maxlength:10"
```

**unsigned** match any numeric value greater then or equal 0

**range** match any numeric value in range a to b

```php
    "input" => "range:1,3",
    "input" => "range:0.3,0.75
```

**idnumber** match any numeric value with 1 to 10 length

**nationalcode** match xxx-xxxxxx-x or xxxxxxxxxx

**jalali** match jalali date time with format

```php
    "input" => "jalali", // default format is Y/m/d
    "input" => "jalali:Y-m-d H:i"
```

**numericarray** match array of numbers

**length** match string with fixed length (input parsed to string).

```php
    "input" => "length:3"
```
