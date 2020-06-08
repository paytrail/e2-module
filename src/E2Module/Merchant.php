<?php

declare(strict_types=1);

namespace Paytrail\E2Module;

/**
 * Class for Merchant data
 *
 * @package e2-module
 * @author Paytrail <tech@paytrail.com>
 */
class Merchant
{
    public $id;
    public $secret;

    /**
     * Create new merchant.
     *
     * @param string $merchantId
     * @param string $merchantSecret
     * @return self
     */
    public static function create(string $merchantId, string $merchantSecret): self
    {
        $merchant = new self();
        $merchant->id = $merchantId;
        $merchant->secret = $merchantSecret;

        return $merchant;
    }
}
