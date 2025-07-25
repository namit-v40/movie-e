<?php

use App\Models\Type;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTypesTable extends Migration
{
    public function up()
    {
        Schema::create('types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->softDeletes();
            $table->timestamps();
        });

        $data = [
            ['name' => 'Phim lẻ', 'slug' => 'phim-le'],
            ['name' => 'Phim bộ', 'slug' => 'phim-bo'],
            ['name' => 'Hoạt hình', 'slug' => 'hoat-hinh'],
            ['name' => 'Truyền hình', 'slug' => 'truyen-hinh'],
        ];

        foreach ($data as $item) {
            Type::create($item);
        }
    }

    public function down()
    {
        Schema::dropIfExists('types');
    }
}