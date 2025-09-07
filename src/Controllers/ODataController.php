<?php

namespace LaravelUi5\Core\Controllers;

use Flat3\Lodata\Controller\Request;
use Flat3\Lodata\Controller\Response;
use Flat3\Lodata\Controller\Transaction;

/**
 * OData Entry Point for UI5
 *
 * This controller handles incoming OData requests from OpenUI5 clients.
 *
 * The OData specification allows for asynchronous processing via the
 * `Prefer: respond-async` header (RFC 7240). However, OpenUI5 does not
 * support or expect this behavior and always relies on immediate, synchronous
 * responses to its OData requests.
 *
 * Therefore, this controller intentionally omits support for `respond-async`
 * and executes all transactions directly.
 *
 * @see Flat3\Lodata\Controller\OData
 */
class ODataController
{
    /**
     * Handle an OData request
     * @param  Request  $request  The request
     * @param  Transaction  $transaction  Injected transaction
     * @return Response Client response
     */
    public function __invoke(Request $request, Transaction $transaction): Response
    {
        $transaction->initialize($request);

        return $transaction->execute();
    }
}
