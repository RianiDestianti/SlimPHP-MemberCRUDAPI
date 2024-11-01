<?php

declare(strict_types=1);

namespace App\Helper;

use Pimple\Psr11\Container;
use Respect\Validation\Validator as v;
use Psr\Http\Message\ServerRequestInterface as Request;

final class Validation
{
    private array $inputs = [];
    private array $rules = [];
    private $request;
    private array $errors = [];

    public function __construct(
        Container $container,
        Request $request,
        $rules
    )
    {
        $this->container = $container;
        $this->request = $request;
        $this->inputs = $request->getParsedBody()?? [];
        $this->rules = $rules;
    }

    public function validate()
    {
        foreach ($this->rules as $name => $rule_string) {
            $rules = explode('|', $rule_string);
            foreach ($rules as $rule) {
                $rule = trim($rule);
                switch ($rule) {
                    case 'required':
                        if(!$this->mustRequired($name)) {
                            goto outer;
                        }
                        break;
                    case (preg_match('/^required_if:[,\w]+$/', $rule) ? true: false):
                        if(!$this->mustRequiredIf($name, explode(':', $rule)[1])) {
                            goto outer;
                        }
                        break;
                    case 'string':
                        $this->mustString($name);
                        break;
                    case 'phone':
                        $this->mustPhone($name);
                        break;
                    case 'email':
                        $this->mustEmail($name);
                        break;
                    case (preg_match('/^max:\d+$/', $rule) ? true: false):
                        $this->mustNotBiggerThanMaxValue($name, (int) explode(':', $rule)[1]);
                        break;
                    case (preg_match('/^min:\d+$/', $rule) ? true: false):
                        $this->mustNotLowerThanMinValue($name, (int) explode(':', $rule)[1]);
                        break;
                    case (preg_match('/^unique:[,\w]+$/', $rule) ? true: false):
                        $this->mustBeUnique($name, explode(':', $rule)[1]);
                        break;
                    case (preg_match('/^in:[,\w]+$/', $rule) ? true: false):
                        $this->mustIncludedInList($name, explode(':', $rule)[1]);
                        break;
                    case (preg_match('/^mimes:[,\w]+$/', $rule) ? true: false):
                        $this->mustIncludedInMimes($name, explode(':', $rule)[1]);
                        break;
                }
            }
            outer:
        }
        $this->throwError();
        return $this->getInputFilteredByRules();
    }

    public function validateGetErrors()
    {
        foreach ($this->rules as $name => $rule_string) {
            $rules = explode('|', $rule_string);
            foreach ($rules as $rule) {
                $rule = trim($rule);
                switch ($rule) {
                    case 'required':
                        if(!$this->mustRequired($name)) {
                            goto outer;
                        }
                        break;
                    case (preg_match('/^required_if:[,\w]+$/', $rule) ? true: false):
                        if(!$this->mustRequiredIf($name, explode(':', $rule)[1])) {
                            goto outer;
                        }
                        break;
                    case 'string':
                        $this->mustString($name);
                        break;
                    case 'phone':
                        $this->mustPhone($name);
                        break;
                    case 'email':
                        $this->mustEmail($name);
                        break;
                    case (preg_match('/^max:\d+$/', $rule) ? true: false):
                        $this->mustNotBiggerThanMaxValue($name, (int) explode(':', $rule)[1]);
                        break;
                    case (preg_match('/^min:\d+$/', $rule) ? true: false):
                        $this->mustNotLowerThanMinValue($name, (int) explode(':', $rule)[1]);
                        break;
                    case (preg_match('/^unique:[,\w]+$/', $rule) ? true: false):
                        $this->mustBeUnique($name, explode(':', $rule)[1]);
                        break;
                    case (preg_match('/^in:[,\w]+$/', $rule) ? true: false):
                        $this->mustIncludedInList($name, explode(':', $rule)[1]);
                        break;
                    case (preg_match('/^mimes:[,\w]+$/', $rule) ? true: false):
                        $this->mustIncludedInMimes($name, explode(':', $rule)[1]);
                        break;
                }
            }
            outer:
        }
        return $this->errors;
    }

private function mustRequired(string $name): bool
    {
        $isNotEmpty = v::notEmpty()->validate($this->getValue($name));
        if (
            !$isNotEmpty
        ) {
            $this->createOrAppend($this->errors, $name, "Kolom ini wajib diisi");
        }
        return $isNotEmpty;
    }

    private function mustRequiredIf(string $name, $spec): bool
    {
        $isNotEmpty = false;
        list($field, $value) = explode(',', $spec);
        if ($this->getValue($field) === $value) {
            if ($this->getValue($name) === "0" || $this->getValue($name) === 0) {
                return true;
            }
            $isNotEmpty = v::notEmpty()->validate($this->getValue($name));
            if (
                !$isNotEmpty
            ) {
                $this->createOrAppend($this->errors, $name, "Kolom ini wajib diisi");
            }
        }
        return $isNotEmpty;
    }

    private function mustString(string $name): void
    {
        if (
            !v::stringType()->validate($this->getValue($name))
        ) {
            $this->createOrAppend($this->errors, $name, "$name value must be a string");
        }
    }

    private function mustPhone(string $name): void
    {
        if (!preg_match("/^\\+?\\d{1,4}?[-.\\s]?\\(?\\d{1,3}?\\)?[-.\\s]?\\d{1,4}[-.\\s]?\\d{1,4}[-.\\s]?\\d{1,9}$/", $this->getValue($name))) {
            $this->createOrAppend($this->errors, $name, "$name value is invalid");
        }
    }

    private function mustEmail(string $name): void
    {
        if (!filter_var($this->getValue($name), FILTER_VALIDATE_EMAIL)) {
            $this->createOrAppend($this->errors, $name, "$name is invalid format");
        }
    }

    private function mustNotBiggerThanMaxValue(string $name, int $max): void
    {
        if (v::type('string')->validate($this->getValue($name))) {
            if (is_numeric($this->getValue($name))) {
                if (
                    !v::max($max)->validate((float) $this->getValue($name))
                ) {
                    $this->createOrAppend($this->errors, $name, "$name value must not bigger than $max");
                }
            } else {
                if (
                    !v::stringType()->length(null, $max)->validate($this->getValue($name))
                ) {
                    $this->createOrAppend($this->errors, $name, "$name must have $max characters at most");
                }
            }
        }
    }

    private function mustNotLowerThanMinValue(string $name, int $min): void
    {
        if (v::type('string')->validate($this->getValue($name))) {
            if (is_numeric($this->getValue($name))) {
                if (
                    !v::min($min)->validate((float) $this->getValue($name))
                ) {
                    $this->createOrAppend($this->errors, $name, "$name value must not lower than $min");
                }
            } else {
                if (
                    !v::stringType()->length($min, null)->validate($this->getValue($name))
                ) {
                    $this->createOrAppend($this->errors, $name, "$name must have $min characters at least");
                }
            }
        }
    }

    private function mustBeUnique(string $name, string $spec): void
    {
        if (v::type('string')->validate($this->getValue($name))) {
            $specs = explode(',', $spec);
            $table = $specs[0];
            $field = $specs[1]?? $name;
            $ignoredId = $specs[2]?? null;

            $model = $this->getDB($table);
            $claims = $this->auth()->parsedToken->claims();

            $queries = $model
            ->where($field, $this->getValue($name))
            ->where('client_id', $claims->get('client_id'))
            ->where('client_secret', $claims->get('client_secret'))
            ->whereNull($this->softDeleteField());

            if ($ignoredId) {
                $queries = $queries->where('id', '<>', $ignoredId);
            }

            if($queries->first()) {
                $this->createOrAppend($this->errors, $name, "$name is already used");
            }
        }
    }

    private function mustIncludedInList(string $name, string $spec): void
    {
        if (v::type('string')->validate($this->getValue($name))) {
            $specs = explode(',', $spec);

            if (
                !v::in($specs)->validate($this->getValue($name))
            ) {
                $this->createOrAppend($this->errors, $name, "$name must one of this lists: ". join(', ', $specs));
            }
        }
    }

    private function mustIncludedInMimes(string $name, string $spec): void
    {
        $specs = explode(',', $spec);

        $extention    = strtolower(pathinfo($_FILES[$name]["name"], PATHINFO_EXTENSION));

        if(in_array($extention, $specs)){

        } else {
            $this->createOrAppend($this->errors, $name, "$name must one of this lists: ". join(', ', $specs));
        }
    }

    private function getValue($name)
    {
        return $this->inputs[$name]?? '';
    }

    private function createOrAppend(&$arr, $key, $value): void
    {
        if (sizeof($arr) > 0) {
            if (isset($arr[$key])) {
                array_push($arr[$key], $value);
            } else {
                $arr[$key] = [$value];
            }
        } else {
            $arr[$key] = [$value];
        }
    }

    private function throwError(): void
    {
        if (sizeof($this->errors)) {
            
            $result = [ 
                'status' => false, 
                'message' => 'Terdapat kesalahan pada data.',
                'data' => $this->errors
            ];

            echo json_encode($result);
            die;
        }
    }
    
    private function getInputFilteredByRules(): array
    {
        return 
            array_filter(
                $this->inputs,
                fn ($key):bool => in_array($key, array_keys($this->rules)),
                ARRAY_FILTER_USE_KEY
            );
    }

    private function getDB($table)
    {
        return $this->container->get('db_read')->getQueryBuilder()->table($table);
    }

    private function softDeleteField()
    {
        return 'deleted_at';
    }

    private function auth()
    {
        return $this->container->get('auth');
    }

}
