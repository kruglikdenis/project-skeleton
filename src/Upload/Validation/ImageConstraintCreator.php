<?php

namespace App\Upload\Validation;


use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints as Assert;

final class ImageConstraintCreator implements FileConstraintCreator
{
    public const MIME_TYPES = [
        'image/jpeg',
        'image/jpg',
        'image/png'
    ];

    public function rule(): Constraint
    {
        return new Assert\Image([
            'maxSize' => '10M',
            'mimeTypes' => static::MIME_TYPES,
            'minWidth' => 50,
            'minHeight' => 50,
            'mimeTypesMessage' => "Allowed file extensions *.jpeg, *.jpg, *.png."
        ]);
    }

    public function support(UploadedFile $file): bool
    {
        return in_array($file->getMimeType(), static::MIME_TYPES);
    }
}