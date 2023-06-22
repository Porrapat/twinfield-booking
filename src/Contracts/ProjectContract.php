<?php

namespace Qlic\Twinfield\Booking\Contracts;

use Carbon\Carbon;
use PhpTwinfield\Enums\ProjectStatus;
use PhpTwinfield\Project;

interface ProjectContract
{
    public function getCode(): ?string;

    public function getShortName(): string;

    public function getName(): string;

    public function getValidFrom(): ?Carbon;

    public function getValidTo(): ?Carbon;

    public function getInvoiceDescription(): ?string;

    public function getAuthoriser(): string;

    public function getCustomer(): string;

    public function getBillable(): bool;

    public function getRate(): ?string;

    public function getQuantities(): ?array;

    /**
     * When creating or updating may be left null;
     * When deleting set it to ProjectStatus::DELETED()
     * @return null|ProjectStatus
     */
    public function getStatus(): ?ProjectStatus;

    public function callback(Project $project);
}
