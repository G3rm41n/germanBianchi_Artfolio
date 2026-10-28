<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Artwork;
use App\Models\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class TrashIndependentTest extends TestCase
{
    use RefreshDatabase;

    /**
     * RF-10: Borrado lógico (SoftDeletes) de obras.
     */
    public function test_artwork_soft_delete_preserves_record_in_database(): void
    {
        $artist = User::factory()->create();
        $artwork = Artwork::factory()->create(['user_id' => $artist->id, 'title' => 'Obra a Papelera']);

        $response = $this->actingAs($artist)->delete("/artworks/{$artwork->id}");
        $response->assertRedirect('/artworks');

        // Sigue existiendo en la tabla con deleted_at
        $this->assertSoftDeleted('artworks', ['id' => $artwork->id]);
        $this->assertDatabaseHas('artworks', ['id' => $artwork->id]);

        // Aparece en la papelera del artista
        $trashResponse = $this->actingAs($artist)->get('/papelera');
        $trashResponse->assertStatus(200);
        $trashResponse->assertSee('Obra a Papelera');
    }

    /**
     * RF-10: Eliminar una colección no destruye ni altera las obras que contiene.
     */
    public function test_deleting_collection_keeps_artworks_intact(): void
    {
        $artist = User::factory()->create();
        $collection = Collection::create([
            'user_id' => $artist->id,
            'name' => 'Colección Destacada',
        ]);

        $artwork = Artwork::factory()->create([
            'user_id' => $artist->id,
            'title' => 'Obra Asignada',
            'status' => 'published',
        ]);

        $collection->artworks()->attach($artwork->id);

        // Se elimina la colección
        $this->actingAs($artist)->delete("/collections/{$collection->id}");

        $this->assertSoftDeleted('collections', ['id' => $collection->id]);

        // La obra permanece activa y no está en la papelera
        $freshArtwork = $artwork->fresh();
        $this->assertNull($freshArtwork->deleted_at);
        $this->assertEquals('published', $freshArtwork->status);
    }

    /**
     * RF-10 / RN-03: Restauración independiente de obra huérfana.
     * Si la colección asociada fue eliminada, al restaurar la obra se desvincula de la colección en papelera.
     */
    public function test_restore_artwork_detaches_from_trashed_collections_rule_rn03(): void
    {
        $artist = User::factory()->create();

        $collection = Collection::create([
            'user_id' => $artist->id,
            'name' => 'Colección en Papelera',
        ]);

        $artwork = Artwork::factory()->create([
            'user_id' => $artist->id,
            'title' => 'Obra Huérfana Restaurada',
            'status' => 'published',
        ]);

        $collection->artworks()->attach($artwork->id);

        // Ambas se envían a la papelera
        $collection->delete();
        $artwork->delete();

        $this->assertSoftDeleted('collections', ['id' => $collection->id]);
        $this->assertSoftDeleted('artworks', ['id' => $artwork->id]);

        // El artista restaura únicamente la obra
        $restoreResponse = $this->actingAs($artist)->post("/papelera/obras/{$artwork->id}/restore");
        $restoreResponse->assertRedirect();

        // 1. La obra ahora está activa (sin deleted_at)
        $refreshedArtwork = $artwork->fresh();
        $this->assertNull($refreshedArtwork->deleted_at);

        // 2. Se desvinculó de la colección eliminada (regla RN-03)
        $this->assertDatabaseMissing('artwork_collection', [
            'artwork_id' => $artwork->id,
            'collection_id' => $collection->id,
        ]);
        $this->assertCount(0, $refreshedArtwork->collections);
    }

    /**
     * RN-04: Purga automática de elementos con más de 30 días de antigüedad con model:prune.
     */
    public function test_pruning_removes_records_older_than_30_days(): void
    {
        $artist = User::factory()->create();

        // Obra reciente en papelera (hace 5 días): NO debe ser purgada
        $recentTrashed = Artwork::factory()->create([
            'user_id' => $artist->id,
            'title' => 'Obra Reciente en Papelera',
        ]);
        $recentTrashed->delete();
        Artwork::withTrashed()->where('id', $recentTrashed->id)->update(['deleted_at' => now()->subDays(5)]);

        // Obra antigua en papelera (hace 35 días): DEBE ser purgada permanentemente
        $oldTrashed = Artwork::factory()->create([
            'user_id' => $artist->id,
            'title' => 'Obra Antigua a Purgar',
        ]);
        $oldTrashed->delete();
        Artwork::withTrashed()->where('id', $oldTrashed->id)->update(['deleted_at' => now()->subDays(35)]);

        // Ejecutar comando de purga de modelos
        Artisan::call('model:prune');

        // La obra reciente permanece en la papelera
        $this->assertDatabaseHas('artworks', ['id' => $recentTrashed->id]);

        // La obra con más de 30 días fue eliminada de forma física/permanente
        $this->assertDatabaseMissing('artworks', ['id' => $oldTrashed->id]);
    }
}
