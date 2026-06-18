<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\CommunityPost;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CommunityPostController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $query = CommunityPost::query()
            ->with(['user:id,name,avatar_initial', 'school:id,name'])
            ->withExists(['likes as liked_by_me' => fn ($likes) => $likes->where('user_id', $user->id)])
            ->when(
                $user->school_id,
                fn ($posts) => $posts->where('school_id', $user->school_id),
                fn ($posts) => $posts->whereNull('school_id'),
            )
            ->when($request->filled('tag'), fn ($posts) => $posts->where('tag', $request->string('tag')))
            ->orderByDesc('is_pinned')
            ->latest();

        return response()->json($query->paginate(min($request->integer('per_page', 20), 100)));
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'tag' => ['nullable', 'string', 'max:80'],
            'content' => ['required', 'string', 'max:5000'],
        ]);

        $post = $request->user()->communityPosts()->create([
            ...$validated,
            'school_id' => $request->user()->school_id,
        ]);

        return response()->json([
            'message' => 'Postingan berhasil dibuat.',
            'data' => $post->load(['user:id,name,avatar_initial', 'school:id,name']),
        ], 201);
    }

    public function destroy(Request $request, CommunityPost $communityPost): JsonResponse
    {
        abort_unless(
            $communityPost->user_id === $request->user()->id || in_array($request->user()->role, ['admin', 'counselor'], true),
            403,
        );

        $communityPost->delete();

        return response()->json(['message' => 'Postingan berhasil dihapus.']);
    }

    public function toggleLike(Request $request, CommunityPost $communityPost): JsonResponse
    {
        $liked = DB::transaction(function () use ($request, $communityPost): bool {
            $like = $communityPost->likes()->where('user_id', $request->user()->id)->first();

            if ($like) {
                $like->delete();
                $liked = false;
            } else {
                $communityPost->likes()->create(['user_id' => $request->user()->id]);
                $liked = true;
            }

            $communityPost->update(['likes_count' => $communityPost->likes()->count()]);

            return $liked;
        });

        return response()->json([
            'message' => $liked ? 'Postingan disukai.' : 'Like dibatalkan.',
            'data' => [
                'liked' => $liked,
                'likes_count' => $communityPost->fresh()->likes_count,
            ],
        ]);
    }
}
