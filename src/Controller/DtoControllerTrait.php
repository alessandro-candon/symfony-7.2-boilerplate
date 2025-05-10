<?php

declare(strict_types=1);

namespace App\Controller;

use App\Exception\System\InvalidPayloadException;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Throwable;

use function count;

trait DtoControllerTrait
{
    public function __construct(
        private readonly SerializerInterface $serializer,
        private readonly ValidatorInterface $validator,
    ) {
    }

    /**
     * @return object|array<int,object>
     * @throws InvalidPayloadException
     */
    public function deserializeAndValidate(string $content, string $class, string $format = 'json'): object|array
    {
        try {
            $deserializedClass = $this->serializer->deserialize($content, $class, $format);
            $violationList = $this->validator->validate($deserializedClass);
        } catch (Throwable $e) {
            throw new InvalidPayloadException($e->getMessage());
        }

        if (count($violationList) > 0) {
            throw new InvalidPayloadException($violationList);
        }

        return $deserializedClass;
    }
}
