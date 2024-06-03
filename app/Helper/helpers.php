<?php

// app/helpers.php

if (!function_exists('encodeId')) {
    /**
     * Encode an ID using base64.
     *
     * @param mixed $id The ID to encode.
     * @return string The encoded ID.
     */
    function encodeId($id)
    {
        return base64_encode($id);
    }
}

if (!function_exists('decodeId')) {
    /**
     * Decode an encoded ID.
     *
     * @param string $encodedId The encoded ID to decode.
     * @return mixed The decoded ID.
     */
    function decodeId($encodedId)
    {
        return base64_decode($encodedId);
    }
}
