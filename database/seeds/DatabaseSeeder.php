<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        //$faker = \Faker\Factory::create();
        // $this->call(UserSeeder::class);
        $this->call(RoleTableSeeder::class);
        $this->call(UserTableSeeder::class);
        $this->call(ExerciseTableSeeder::class);
        $this->call(SupplementTableSeeder::class);
        
        /*Factory(App\Models\Recipe::class,20)->create()->each(function($u) use($faker) {
            $image = new \App\Models\Image;
            $image->url = $faker->imageUrl($width = 640, $height = 480);
            $image->type = 'recipe_image';
            $u->images()->save($image);

            $recipe_audio = new \App\Models\Image;
            $recipe_audio->url = 'public/uploads/recipe/1_MP3_700KB.mp3';
            $recipe_audio->type = 'recipe_audio';
            $u->images()->save($recipe_audio);

            $recipe_audio = new \App\Models\Image;
            $recipe_audio->url = 'public/uploads/recipe/2_OOG_1MG.ogg';
            $recipe_audio->type = 'recipe_audio';
            $u->images()->save($recipe_audio);

            $recipe_video = new \App\Models\Image;
            $recipe_video->url = 'public/uploads/recipe/4-mp4-file.mp4';
            $recipe_video->type = 'recipe_video';
            $u->images()->save($recipe_video);
        });*/
    }
}
