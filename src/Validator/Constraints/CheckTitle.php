<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Compound;


class CheckTitle extends  Compound
{
    protected function getConstraints(array $options): array
    {
        return [
            new Assert\Length([
                'min' => 3,
                'max' => 50,
                'minMessage' => 'Your title should contain at list {{ limit }} letters',
                'maxMessage' => 'Your title cant be longer than  {{ limit }} letters'
            ]),
            new Assert\Regex([
                'pattern' => '/\d/',
                'match' => false,
                'message' => 'Your title shoud not contain numbers'
            ])
        ];
    }
}
