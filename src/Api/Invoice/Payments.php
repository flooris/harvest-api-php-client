<?php
/**
 * Payments class.
 */

namespace Required\Harvest\Api\Invoice;

use DateTime;
use Required\Harvest\Api\AbstractApi;

/**
 * API client for invoice payments endpoint.
 *
 * @link https://help.getharvest.com/api-v2/invoices-api/invoices/invoice-payments/
 */
class Payments extends AbstractApi implements PaymentsInterface {


	/**
	 * Retrieves a list of invoice payments for a specific invoice.
	 *
	 * @throws \Http\Client\Exception
	 *
	 * @param int   $invoiceId  The ID of the invoice.
	 * @param array $parameters {
	 *     Optional. Parameters for filtering the list of invoice payments. Default empty array.
	 *
	 *     @type DateTime|string $updated_since  Only return invoice payments that have been updated since the given
	 *                                           date and time.
	 * }
	 * @return array|string
	 */
	public function all( int $invoiceId, array $parameters = [] ) {
		if ( isset( $parameters['updated_since'] ) && $parameters['updated_since'] instanceof DateTime ) {
			$parameters['updated_since'] = $parameters['updated_since']->format( DateTime::ATOM );
		}

		$result = $this->get( '/invoices/' . rawurlencode( $invoiceId ) . '/payments', $parameters );
		if ( ! isset( $result['invoice_payments'] ) || ! \is_array( $result['invoice_payments'] ) ) {
			throw new \Required\Harvest\Exception\RuntimeException( 'Unexpected result.' );
		}

		return $result['invoice_payments'];
	}

	/**
	 * Retrieves the invoice message with the given ID.
	 *
	 * @throws \Http\Client\Exception
	 *
	 * @param int $invoiceId The ID of the invoice.
	 * @param int $messageId The ID of the invoice message.
	 * @return array|string
	 */
	public function show( int $invoiceId, int $messageId ) {
		return $this->get( '/invoices/' . rawurlencode( $invoiceId ) . '/payments/' . rawurlencode( $messageId ) );
	}

	/**
	 * Creates a new invoice message object.
	 *
	 * @throws \Http\Client\Exception
	 * @throws \Required\Harvest\Exception\MissingArgumentException
	 * @throws \Required\Harvest\Exception\InvalidArgumentException
	 *
	 * @param int   $invoiceId The ID of the invoice.
	 * @param array $parameters The parameters of the new invoice message object.
	 * @return array|string
	 */
	public function create( int $invoiceId, array $parameters ) {
		if ( ! isset( $parameters['amount'] ) ) {
			throw new \Required\Harvest\Exception\MissingArgumentException( 'amount' );
		}

		if ( ! \is_float( $parameters['amount'] ) || empty( $parameters['amount'] ) ) {
			throw new \Required\Harvest\Exception\InvalidArgumentException( 'The "amount" parameter must be a non-empty decimal.' );
		}

		return $this->post( '/invoices/' . rawurlencode( $invoiceId ) . '/payments', $parameters );
	}

	/**
	 * Deletes an invoice message.
	 *
	 * @throws \Http\Client\Exception
	 *
	 * @param int $invoiceId The ID of the invoice.
	 * @param int $messageId  The ID of the invoice message.
	 * @return array|string
	 */
	public function remove( int $invoiceId, int $messageId ) {
		return $this->delete( '/invoices/' . rawurlencode( $invoiceId ) . '/payments/' . rawurlencode( $messageId ) );
	}
}
