<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'content' => $this->content,
            'user' => $this->when($this->relationLoaded('user') && $this->user, function () {
                return new UserResource($this->user);
            }),
            'comments' => $this->when($this->relationLoaded('comments') && $this->comments, function () {
                return CommentResource::collection($this->comments);
            }),
            'comments_count' => $this->when(isset($this->comments_count), $this->comments_count),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'created_at_human' => $this->created_at->diffForHumans(),
            'updated_at_human' => $this->updated_at->diffForHumans(),
        ];
    }
}