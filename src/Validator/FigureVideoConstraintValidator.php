<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class FigureVideoConstraintValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        /* @var $constraint \App\Validator\FigureVideoConstraint */

        if (null === $value || '' === $value) {
            return;
        }

        if (preg_match('#^https://www\.youtube\.com/embed#', $value)) {
            return;
        }
        if (preg_match('#^https://www\.dailymotion\.com/embed/video#', $value)) {
            return;
        }
        if (preg_match('<iframe(?:\b|_).*?(?:\b|_)src=\"https:\/\/www.(youtube|dailymotion).com\/(?:\b|_).*?(?:\b|_)iframe>', $value)) {
            return;
        }

        $this->context->buildViolation($constraint->message)
            ->setParameter('{{ value }}', $value)
            ->addViolation();
    }
}
