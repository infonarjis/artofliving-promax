    <?php

    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    class CreateAdvertisementRequestsTable extends Migration
    {
        /**
         * Run the migrations.
         *
         * @return void
         */
        public function up()
        {
            Schema::create('advertisement_inquiry', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('email')->unique();
                $table->string('mobile')->nullable();
                $table->text('description')->nullable();
                $table->enum('status', ['PENDING', 'APPROVED', 'UNAPPROVED'])->default('PENDING');
                $table->timestamps();
                $table->enum('is_deleted', ['Yes', 'No'])->default('No');
            });
        }

        /**
         * Reverse the migrations.
         *
         * @return void
         */
        public function down()
        {
            Schema::dropIfExists('advertisement_inquiry');
        }
    }
