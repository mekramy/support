<?php

namespace MEkramy\Support;

use MEkramy\PHPUtil\Helpers;
use MEkramy\OOPUtil\CanChained;
use MEkramy\OOPUtil\MapGetterSetter;

/**
 * Rate limiter
 * Keyed rate limiter based on ip
 *
 * @author m ekramy <m@ekramy.ir>
 * @access public
 * @version 1.0.0
 */
class RateLimiter
{
    use MapGetterSetter, CanChained;

    /**
     * Request instance
     *
     * @var \Illuminate\Http\Request
     */
    protected $request;

    /**
     * Rate limiter instance
     *
     * @var \Illuminate\Cache\RateLimiter
     */
    protected $limiter;

    /**
     * limiter original key
     *
     * @var string
     */
    protected $key;

    /**
     * max allowed attempts
     *
     * @var int
     */
    protected $maxAttempts;

    /**
     * decay seconds range for max attempts
     *
     * @var int
     */
    protected $decaySeconds;

    /**
     * Create a new RateLimit instance.
     *
     * @param \Illuminate\Http\Request $request             request instance
     * @param \Illuminate\Cache\RateLimiter $limiter        limiter instance
     * @return void
     */
    public function __construct(\Illuminate\Http\Request $request, \Illuminate\Cache\RateLimiter $limiter)
    {
        $this->request = $request;
        $this->limiter = $limiter;
        $this->setMaxAttempts(3);
        $this->setDecaySeconds(300);
    }

    /**
     * Methods list to exclude in chaining call
     *
     * @return array
     */
    protected function __cantChain(): array
    {
        return ['getKey', 'getMaxAttempts', 'getDecaySeconds', 'mustLock', 'retriesLeft', 'availableIn'];
    }

    /**
     * Set all states at once
     *
     * @param string $key                                   limiter key
     * @param int $maxAttempts                              max available attempts
     * @param int $decaySeconds                             limiter ttl in seconds. after this seconds limiter was cleared
     * @return void
     */
    public function init(string $key, int $maxAttempts, int $decaySeconds): void
    {
        $this->setKey($key);
        $this->setMaxAttempts($maxAttempts);
        $this->setDecaySeconds($decaySeconds);
    }

    /**
     * Set key
     *
     * @param string $key
     * @return void
     */
    public function setKey(string $key)
    {
        $this->key = $key;
    }

    /**
     * Get key
     *
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * Set max attempts
     *
     * @param int $maxAttempts
     * @return void
     */
    public function setMaxAttempts(int $maxAttempts)
    {
        $this->maxAttempts = $maxAttempts;
    }

    /**
     * Get max attempts
     *
     * @return int
     */
    public function getMaxAttempts(): int
    {
        return $this->maxAttempts;
    }

    /**
     * Set decay seconds
     *
     * @param int $decaySeconds
     * @return void
     */
    public function setDecaySeconds(int $decaySeconds)
    {
        $this->decaySeconds = $decaySeconds;
    }

    /**
     * Get decay seconds
     *
     * @return int
     */
    public function getDecaySeconds(): int
    {
        return $this->decaySeconds;
    }

    /**
     * Check whatever request must locked or not
     *
     * @return bool
     */
    public function mustLock(): bool
    {
        return $this->limiter->tooManyAttempts($this->realKey(), $this->maxAttempts);
    }

    /**
     * Increase the number of attempts
     *
     * @return int                                          return current hits count
     */
    public function addAttempts(): int
    {
        return (int) $this->limiter->hit($this->realKey(), $this->decaySeconds);
    }

    /**
     * lock rate limiter
     *
     * @return void
     */
    public function lock()
    {
        for ($i = 0; $i < $this->maxAttempts; $i++) {
            @$this->addAttempts();
        }
    }

    /**
     * Reset the number of attempts
     *
     * @return mixed
     */
    public function reset()
    {
        return $this->limiter->resetAttempts($this->realKey());
    }

    /**
     * Get the number of retries left
     *
     * @return int
     */
    public function retriesLeft(): int
    {
        return $this->limiter->retriesLeft($this->realKey(), $this->maxAttempts);
    }

    /**
     * Return seconds until unlock
     *
     * @return int
     */
    public function availableIn(): int
    {
        return $this->limiter->availableIn($this->realKey());
    }

    /**
     * Get real key by combining key and ip
     *
     * @return string
     */
    private function realKey(): string
    {
        return Helpers::formatString("{key}::{ip}", ["key" => $this->_key, "ip" => $this->request->ip()]);
    }
}
