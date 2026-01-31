<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Property;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class PropertyImageController extends Controller
{
    /**
     * Upload images to property.
     */
    public function store(Request $request, Property $property): RedirectResponse
    {
        $this->authorize('update', $property);

        $request->validate([
            'images' => ['required', 'array', 'min:1'],
            'images.*' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'], // 5MB max
        ], [
            'images.required' => 'Pilih minimal 1 foto',
            'images.*.image' => 'File harus berupa gambar',
            'images.*.mimes' => 'Format gambar harus JPG, PNG, atau WebP',
            'images.*.max' => 'Ukuran gambar maksimal 5MB',
        ]);

        foreach ($request->file('images') as $image) {
            $property->addMedia($image)
                ->toMediaCollection('images');
        }

        return back()->with('success', 'Foto berhasil diupload');
    }

    /**
     * Reorder images.
     */
    public function reorder(Request $request, Property $property): RedirectResponse
    {
        $this->authorize('update', $property);

        $request->validate([
            'order' => ['required', 'array'],
            'order.*' => ['required', 'integer'],
        ]);

        foreach ($request->order as $index => $mediaId) {
            Media::where('id', $mediaId)
                ->where('model_id', $property->id)
                ->update(['order_column' => $index + 1]);
        }

        return back()->with('success', 'Urutan foto berhasil diubah');
    }

    /**
     * Delete image.
     */
    public function destroy(Property $property, Media $media): RedirectResponse
    {
        $this->authorize('update', $property);

        // Ensure media belongs to this property
        if ($media->model_id !== $property->id) {
            abort(404);
        }

        $media->delete();

        return back()->with('success', 'Foto berhasil dihapus');
    }
}
