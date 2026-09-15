<?php
  
namespace App\Rules;
  
use Illuminate\Contracts\Validation\Rule;
  
class CheckMaxValue implements Rule
{
    protected $otherField, $otherFieldValue,$attribute_name = '';

    public function __construct($otherFieldValue, $otherField)
    {
        $this->otherField = $otherField;
        $this->otherFieldValue = $otherFieldValue;
    }

    public function passes($attribute, $value)
    {
        // $otherFieldValue = $this->otherField;
        return $value >= $this->otherFieldValue;
    }

   
    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        // return 'The :attribute is not match.';
       
        if($this->otherField == 'sq ft min'){
            return 'The sq ft max must be greater than or equal ' . str_replace('_', ' ', $this->otherField);
        }else{
            return 'The :attribute must be greater than or equal ' . str_replace('_', ' ', $this->otherField);
        }
       
    }
}