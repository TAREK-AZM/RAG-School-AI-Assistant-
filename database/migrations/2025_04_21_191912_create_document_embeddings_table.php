<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateDocumentEmbeddingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // First, enable the pgvector extension
        DB::statement('CREATE EXTENSION IF NOT EXISTS vector');
        
        Schema::create('document_embeddings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('document_id');
            $table->text('content');
            $table->integer('chunk_index');
            $table->timestamps();
            
            $table->foreign('document_id')
                  ->references('id')
                  ->on('documents')
                  ->onDelete('cascade');
        });
        
        // Add vector column for embeddings (1536 dimensions for OpenAI embeddings)
        DB::statement('ALTER TABLE document_embeddings ADD COLUMN embedding vector(768)');
       
        // Add index for faster similarity search
        DB::statement('CREATE INDEX ON document_embeddings USING hnsw (embedding vector_cosine_ops)');    

        // Test inserting a vector - CORRECTED VERSION
        // $values = implode(',', array_fill(0, 1536, 0.01));
        // DB::statement("
        //     INSERT INTO document_embeddings 
        //     (document_id, content, chunk_index, embedding)
        //     VALUES (?, ?, ?, ?::vector)",
        //     [
        //         1, 
        //         'Test content', 
        //         0,
        //         "[$values]" // Proper vector format with brackets
        //     ]
        // );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('document_embeddings');
    }
}