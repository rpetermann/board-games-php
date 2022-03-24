<?php

namespace App\Services\Workflow;

use App\Entity\WorkflowableInterface;
use App\Services\Workflow\Exception\WorkflowException;
use Symfony\Component\Workflow\Registry;
use Symfony\Component\Workflow\Workflow;

/**
 * WorkflowService
 */
class WorkflowService
{
    /**
     * @var array
     */
    protected $workflows = [];

    /**
     * @var Registry
     */
    protected $registry;

    /**
     * __construct
     *
     * @param  Registry $registry
     * @return void
     */
    public function __construct(Registry $registry)
    {
        $this->registry = $registry;
    }

    /**
     * changeStatus
     *
     * @param  string                $transition
     * @param  WorkflowableInterface $subject
     * @param  array                 $context
     * @return void
     */
    public function changeStatus(string $transition, WorkflowableInterface $subject, array $context = []): void
    {
        $status = $subject->getState();
        if (!$this->can($transition, $subject)) {
            throw WorkflowException::fromTransitionApply($transition, $status);
        }

        $this->apply($transition, $subject, $context);
    }

    /**
     * multiChangeStatus
     *
     * @param  string                $transition
     * @param  WorkflowableInterface ...$subjects
     * @return void
     */
    public function multiChangeStatus(string $transition, WorkflowableInterface ...$subjects): void
    {
        foreach ($subjects as $subject) {
            $this->changeStatus($transition, $subject);
        }
    }

    /**
     * can
     *
     * @param  string                $transition
     * @param  WorkflowableInterface $subject
     * @return bool
     */
    public function can(string $transition, WorkflowableInterface $subject): bool
    {
        return $this->getWorkflow($subject)->can($subject, $transition);
    }

    /**
     * apply
     *
     * @param  string                $transition
     * @param  WorkflowableInterface $subject
     * @param  array                 $context
     * @return void
     */
    private function apply(string $transition, WorkflowableInterface $subject, array $context = [])
    {
        return $this->getWorkflow($subject)->apply($subject, $transition, $context);
    }

    /**
     * getWorkflow
     *
     * @param  WorkflowableInterface $subject
     * @return Workflow
     */
    private function getWorkflow(WorkflowableInterface $subject): Workflow
    {
        $className = get_class($subject);
        if (!isset($this->workflows[$className])) {
            $this->workflows[$className] = $this->registry->get($subject);
        }

        return $this->workflows[$className];
    }
}
