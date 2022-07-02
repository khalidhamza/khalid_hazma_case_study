<?php

    namespace App\Http\Traits;
    /**
     * API Response Trait
     *
     * @author Khalid Hamza <khalid_hamza2015@yahoo.com> <2022-07-02 22:15>
     */
    trait ApiResponse {

        /**
         * Api Results
         *
         * @param object $data
         * @param string|null $message
         *
         * @return array [status, code, message, data, errors]
         */
        public function apiResults($code = 200, $message = 'success', $data = null, $errors = null)
        {
            return response()->json([
                'status'    => true,
                'code'      => $code,
                'message'   => __("api.{$message}"),
                'result'    => $data,
                'errors'    => $errors,
            ]);
        }

        /**
         * Api Validation Errors
         *
         * @param array $errors
         *
         * @return array [status, code, message, data, errors]
         */
        public function apiValidationErrors($errors)
        {
            return response()->json(
                [
                    'status'        => false,
                    'code'          => 115,
                    'message_en'    => $this->getSingleError($errors),
                    'message_ar'    => $this->getSingleError($errors),
                    'result'        => null,
                    'errors'        => $errors
                ]
            );
        }

        /**
         * Get Single Error
         *
         * @param array $errors
         *
         * @return string $singleError
         */
        public function getSingleError($errors)
        {
            $error    = reset($errors);
            if(is_array($error)){
                $first_key  = key($error);
                $error      = $error[$first_key];
            }
            return $error;
        }

    }
?>
