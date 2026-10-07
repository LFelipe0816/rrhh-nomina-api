<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('job_requisitions', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('job_id');
            $table->foreign('job_id')
                ->references('id')
                ->on('company_jobs')
                ->onDelete('restrict');

//            Historic Job descriptions
//            $table->unsignedBigInteger('jd_version_id');
//            $table->foreign('jd_version_id')
//                ->references('version_id')
//                ->on('jd_versions')
//                ->onDelete('restrict');

            $table->unsignedBigInteger('requesting_manager_id')->nullable();
            $table->foreign('requesting_manager_id')
                ->references('id')
                ->on('employees')
                ->onDelete('set null');

            $table->date('open_date');
            $table->date('expected_close_date')->nullable();
            $table->integer('positions_count')->default(1);
            $table->string('status', 50)->default('Open')->comment('Open, In process, Completed, Canceled');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('job_requisitions');
    }
};
