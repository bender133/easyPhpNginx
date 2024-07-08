<?php

class Signature
{
    const DEPOSIT_ORDER = ['id', 'txnId', 'amount'];
    const PAYOUT_ORDER  = ['amount', 'id'];
    const ERROR_ORDER   = ['id', 'txnId'];

    /**
     * @param array  $data
     * @param array  $meta
     * @param string $signature
     * @param int    $transactionType
     * @param string $apiToken
     *
     * @return bool
     */
    public static function check(array $data, array $meta, string $signature, int $transactionType, string $apiToken): bool
    {
        $sequence = [];

        foreach (self::getSequence($data, $meta, $transactionType) as $field) {
            if (isset($data[$field]) && $data[$field] !== null) {
                $sequence[] = $data[$field];
            }
        }

        $str       = implode(':', $sequence);
        $generated = hash_hmac('sha256', $str, $apiToken);

        return $signature === $generated;
    }

    /**
     * @param array $data
     * @param array $meta
     * @param int   $transactionType
     *
     * @return string[]
     */
    private static function getSequence(array $data, array $meta, int $transactionType): array
    {
        if ($transactionType === 0) {
            return self::DEPOSIT_ORDER;
        }

        if ($transactionType === 1) {
            return self::PAYOUT_ORDER;
        }

        return self::ERROR_ORDER;
    }
}


$jsonData = '{
    "data": {
        "amount": 3076.65,
        "id": "4a4049b7-56d1-40a0-b001-a83f362283b4",
        "currency": "RUB"
    },
    "meta": {
        "code": "CANCEL"
    }
}';

$data = json_decode($jsonData, true);

$data['signature']       = '8c482103a7ded8ea3bef0c9e8a55bbc6efa11bf329e47d0ae11548b41d04c196';
$data['transactionType'] = 1;
$data['apiToken']        = 'GOGATEWAY.bdd27ad3b610f3608f712177c04c6f79868083fa5da662461a3317cf99c770d1';

$isValid = Signature::check($data['data'], $data['meta'], $data['signature'], $data['transactionType'], $data['apiToken']);

if ($isValid) {
    echo "Signature is valid.";
} else {
    echo "Signature is invalid.";
}