<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class SocialMediaLink implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        // Anda dapat mengganti logika berikut sesuai dengan kebutuhan Anda.
        // Ini adalah contoh sederhana untuk mendeteksi tautan Instagram atau LinkedIn.
        return preg_match('/^(https?:\/\/)?(www\.)?(instagram\.com|linkedin\.com)/', $value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Tautan harus menjadi tautan Instagram atau LinkedIn yang valid.';
    }
}
