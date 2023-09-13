<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Compound;


class CheckDescription extends  Compound
{
    protected function getConstraints(array $options): array
    {
        return [
            new Assert\Length([
                'min' => 3,
                'max' => 500,
                'minMessage' => 'Your description should contain at list {{ limit }} letters',
                'maxMessage' => 'Your description cant be longer than  {{ limit }} letters'
            ]),
            new Assert\Regex([
                'pattern' => '/<[^>]*>/', // This regex pattern matches any HTML tag
                'match' => false,         // Set to false to make sure it doesn't match
                'message' => 'Description should not contain HTML tags.',
            ]),
        ];
    }
}
