<?php


namespace AspectOverride\Filters;


use AspectOverride\Facades\Registry;

class FunctionLoader extends AbstractFilter
{

    public function getName(): string
    {
        return "FUNCTION_LOADER";
    }

    public function process(string $chunk, int $length)
    {
        $re = "/(namespace)(.+?)(;)/";
        if(preg_match($re, $chunk, $matches)){
            $namespace = trim($matches[2]);
            $this->loadFunctions($namespace);
        }
    }

    public function loadFunctions(string $namespace): void
    {
        foreach (Registry::getFunctions() as $function) {
            $code = "
            namespace {$namespace} {
              if(!function_exists('\\$namespace\\$function')) {
                function {$function}() {
                  if(\$__fn__ = \AspectOverride\Facades\Registry::getForFunction('$function')) {
                    return \$__fn__(...func_get_args());
                  }
                }
              }
            }";
            eval($code);
        }
    }
}