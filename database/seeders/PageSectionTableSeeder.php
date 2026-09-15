<?php

namespace Database\Seeders;

use App\Models\Section;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PageSectionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // DB::table('sections')->truncate();

        $sections = [

            [
                'page_id'       => 7,
                'title'         => 'Section 1',
                'section_key'   => 'section_one',
                'content_text'  => 'At TrueYou, our mission is rooted in a deep belief in the extraordinary potential that exists within every individual. We are dedicated to empowering people from all walks of life to unleash their innate abilities, fulfill their dreams, and reach new heights of personal and professional success.',

                'other_details'     => null,
                'is_image'          => 0,
                'is_multiple_image' => 0,
                'is_video'          => 0,
                'button'            => null,
                'status'            => 1,
                'created_at'     => date('Y-m-d H:i:s'),
                'updated_at'     => date('Y-m-d H:i:s'),
            ],

            [
                'page_id'       => 7,
                'title'         => 'Section 2',
                'section_key'   => 'section_two',
                'content_text'  => 'We firmly believe that every dream, no matter how big or small, deserves to be pursued. Our organization serves as a catalyst for transformation, offering a range of programs, resources, and mentorship opportunities designed to inspire and empower individuals to take bold steps towards their aspirations.',

                'other_details'     => null,
                'is_image'          => 1,
                'is_multiple_image' => 0,
                'is_video'          => 0,
                'button'            => null,
                'status'            => 1,
                'created_at'     => date('Y-m-d H:i:s'),
                'updated_at'     => date('Y-m-d H:i:s'),
            ],

            [
                'page_id'       => 7,
                'title'         => 'Section 3',
                'section_key'   => 'section_three',
                'content_text'  => 'We are driven by a vision of a world where every person is empowered to pursue their passions, unleash their creativity, and make a meaningful contribution. Our work is guided by a deep sense of purpose and a commitment to fostering an inclusive, equitable, and supportive environment for all. We recognize and celebrate the diverse talents, backgrounds, and aspirations of the people we serve, understanding that true potential knows no boundaries',

                'other_details'     => null,
                'is_image'          => 1,
                'is_multiple_image' => 0,
                'is_video'          => 0,
                'button'            => null,
                'status'            => 1,
                'created_at'     => date('Y-m-d H:i:s'),
                'updated_at'     => date('Y-m-d H:i:s'),
            ],

        ];

        Section::insert($sections);
    }
}
