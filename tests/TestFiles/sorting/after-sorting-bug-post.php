<?php

declare(strict_types=1);

namespace RequestRepeater\Backend\RestControllers\Requests\Request;

use Medas\EntityManager\{EntityManager, Exceptions\PropertyDoesNotExist};
use Medas\HttpRequestHandler\{Exceptions\RequestNotAuthorized, RequestFactory};
use Medas\RestRequestHandler\{Exceptions\EntityDoesNotHaveProperty, Responses\EntityResponse};
use Medas\Routing\{Methods\Post, Route};
use RequestRepeater\Backend\Requests\Request;

#[Route('requests')]
readonly class CreateIt
{
    public function __construct(
        private EntityManager        $entityManager,
        private Helpers\ItNormalizer $normalizer,
        private RequestFactory       $RequestFactory,
    )
    {
    }

    #[Post]
    public function handle(): EntityResponse
    {
        $data = $this->RequestFactory->get()->bodyData->data();
        $data = $this->normalizer->unserializeAndDenormalize($data);

        allowElseThrow(
            $vote = new Authorization\CreateItVote($data),
            new RequestNotAuthorized($vote->allowedAccess)
        );

        try {
            $entity = $this->entityManager->create(Request::class, $data);
        }
        catch (PropertyDoesNotExist $exception) {
            throw new EntityDoesNotHaveProperty(Request::class, $exception->propertyName);
        }

        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        $data = $this->normalizer->normalizeAndSerialize($entity);

        return new EntityResponse($data);
    }
}
