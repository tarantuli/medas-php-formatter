<?php

declare(strict_types=1);

namespace RequestRepeater\Backend\RestControllers\Requests\Request;

use Medas\EntityManager\{EntityManager, Exceptions\PropertyDoesNotExist};
use Medas\HttpRequestHandler\{Exceptions\RequestNotAuthorized, RequestFactory};
use Medas\RestRequestHandler\{
    Exceptions\EntityDoesNotHaveProperty,
    Responses\EntityResponse
};
use Medas\Routing\{Methods\Post, Route};

#[Route('requests')]
readonly class CreateIt
{
    public function __construct(
        private EntityManager$entityManager,
        private RequestFactory $RequestFactory,
        private \RequestRepeater\Backend\RestControllers\Requests\Request\Helpers\ItNormalizer $normalizer,
    )
    {
    }

    #[Post]
    public function handle(): EntityResponse
    {
        $data = $this->RequestFactory->get()->bodyData->data();
        $data = $this->normalizer->unserializeAndDenormalize($data);

        allowElseThrow(
            $vote = new \RequestRepeater\Backend\RestControllers\Requests\Request\Authorization\CreateItVote($data),
            new RequestNotAuthorized($vote->allowedAccess)
        );

        try {
            $entity = $this->entityManager->create(\RequestRepeater\Backend\Requests\Request::class, $data);
        }
        catch (PropertyDoesNotExist $exception) {
            throw new EntityDoesNotHaveProperty(\RequestRepeater\Backend\Requests\Request::class, $exception->propertyName);
        }

        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        $data = $this->normalizer->normalizeAndSerialize($entity);

        return new EntityResponse($data);
    }
}
