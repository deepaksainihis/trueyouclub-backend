<?php
  
namespace App\Rules;
  
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Hash;
  
class CheckMinValue implements Rule
{
    protected $otherField, $otherFieldValue;

    public function __construct($otherFieldValue, $otherField)
    {
        $this->otherField = $otherField;
        $this->otherFieldValue = $otherFieldValue;
    }

    public function passes($attribute, $value)
    {
        // $otherFieldValue = $this->otherField;
        
        return $value <= $this->otherFieldValue;
    }

   
    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {

        if($this->otherField == 'sq ft max'){
            return 'The sq ft min must be less than or equal ' . str_replace('_', ' ', $this->otherField);
        }else{
            return 'The :attribute must be less than or equal ' . str_replace('_', ' ', $this->otherField);
        }

        // return 'The :attribute is not match.';
    }
}