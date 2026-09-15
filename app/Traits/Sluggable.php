<?php
  namespace App\Traits;

    trait Sluggable
    {
        public function generateSlug($string)
        {
            return strtolower(preg_replace(
                ['/[^\w\s]+/', '/\s+/'],
                ['', '-'],
                $string
            ));
        }
    }