<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Tonysm\RichTextLaravel\Models\Traits\HasRichText;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Recipe extends Model
{
    use HasFactory;
    use HasRichText;

    /**
     * @var string[]
     */
    protected $fillable = [
        'name',
        'description',
        'ingredients',
        'instructions',
        'featured_image',
        'tags',
        'active',
        'import_url',
    ];

    /**
     * @var array
     */
    protected $guarded = [];

    /**
     * @var string[]
     */
    protected $richTextAttributes = ['description', 'ingredients', 'instructions'];

    /**
     * Set the tags attribute to a JSON-encoded value. The field is a jsonb column in the database.
     *
     * @param $value
     * @return void
     */
    public function setTagsAttribute($value)
    {
        $value = array_map('trim', explode(',', $value));
        $this->attributes['tags'] = json_encode($value);
    }

    public function getTagsAttribute($value)
    {
        $value = json_decode($value);
        return $value;
    }

    public function scopeCurrentUser($query)
    {
        return $query->where('user_id', auth()->id());
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
