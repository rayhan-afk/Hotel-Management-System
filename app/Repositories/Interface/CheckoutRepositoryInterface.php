<?php

namespace App\Repositories\Interface;

interface CheckoutRepositoryInterface
{
    public function getCheckoutDatatable($request);

    public function getTransaction($id);

    public function checkoutDelete($id);
}
