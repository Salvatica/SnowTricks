<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
#[\Attribute] class FigureVideoConstraint extends Constraint
{
    /*
     * Any public properties become valid options for the annotation.
     * Then, use these in your validator class.
     */
    public $message = 'Vous devez saisir un code iframe ou une url valide (youtube / dailymotion)';

    public function validatedBy()
    {
        return static::class.'Validator';
    }
}
