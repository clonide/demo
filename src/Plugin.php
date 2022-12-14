<?php

namespace Plugide\Define;

use Illuminate\Contracts\Routing\UrlRoutable;
use Plugide\Define\Contracts\Plugable;
use Plugide\Define\Support\Concerns;
use Symfony\Component\Yaml\Yaml;

/**
 * @property mixed exists
 * @property mixed type
 */
class Plugin extends Prototype implements Plugable, UrlRoutable
{
    use Concerns\HasCommon;
    use Concerns\HasEvents;
    use Concerns\HasRouting;

    /**
     * Collect the plugins
     *
     * @return array|null
     */
    public function collect()
    {
        return Plug::plugins($this->type);
    }

    /**
     * Find file for plugin asset.
     *
     * @param string $path
     * @return mixed|string
     */
    public function assets(string $path): string
    {
        return route(Plug::data('assets.name'), [$this->get(Plug::data('assets.key') ?? 'handle'), $path]);
    }

    /**
     * Get the namespace
     *
     * @param string|null $space
     * @return mixed|string
     */
    public function namespace(string $space = null)
    {
        $namespace = $this->type()->namespace;

        if ($this->exists) {
            $namespace = $this->get('namespace');
        }

        if ($space) {
            $namespace = $namespace."\\".$space;
        }

        return $namespace;
    }

    /**
     * Path plugin.
     *
     * @param string|null $filename
     * @return mixed|string
     */
    public function path(string $filename = null)
    {
        $path =  $this->type()->path;

        if ($this->exists) {
            $path = dirname($this->common('file')->getPathname());
        }

        if ($filename) {
            return $path.'/'.$filename;
        }

        return $path;
    }

    /**
     * Type associated with the plugin's.
     *
     * @param null $type
     * @return \Plugide\Define\Contracts\Typable|static|null
     */
    public function type($type = null)
    {
        if (is_null($type)) {
            return Plug::findType($this->type);
        }

        $this->type = $type;

        return $this;
    }

    /**
     * Write file yml.
     *
     * @return false|int
     */
    public function write()
    {
        $yaml = Yaml::dump($this->getAttributes(), 100, 2, Yaml::DUMP_MULTI_LINE_LITERAL_BLOCK);

        return file_put_contents($this->common('file')->getPathname(), $yaml);
    }
}
