<?php

namespace App\Http\Controllers;

use App\Http\Resources\LinkCollection;
use App\Http\Resources\LinkResource;
use App\Models\Link;
use App\Http\Requests\StoreLinkRequest;
use App\Http\Requests\UpdateLinkRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class LinkController extends Controller
{
    function generateShortToken($userId): string
    {
        $data = $userId . microtime();
        $hash = Hash::make($data);
        $hash = preg_replace('/[^a-zA-Z0-9]/', '', $hash);
        $token = substr($hash, 0, 6);
        return $token;
    }

    function generateUniqueShortToken($userId): string
    {
        do {
            $shortToken = $this->generateShortToken($userId);
        } while (Link::where('short_token', $shortToken)->exists());
        return $shortToken;
    }

    /**
     * @OA\Get(
     *     path="/api/links",
     *     summary="Get a list of public links for authenticated user",
     *     tags={"Link"},
     *     security={
     *           {"sanctum": {}},
     *      },
     *     @OA\Response(response=200, description="Successful operation"),
     *     @OA\Response(response=400, description="Invalid request")
     * )
     */
    public function index(Request $request): LinkCollection
    {
        $this->authorize('viewAny', Link::class);
        $user = $request->user();
        $links = $user->links->where('is_private', 0);
        return new LinkCollection($links);
    }


    /**
     * @OA\Post(
     *     path="/api/links",
     *     summary="Create Link",
     *     tags={"Link"},
     *     security={
     *           {"sanctum": {}},
     *      },
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(ref="#/components/schemas/StoreLinkRequest")
     *     ),
     *     @OA\Response(response=200, description="Successful operation"),
     *     @OA\Response(response=400, description="Invalid request")
     * )
     */
    public function store(StoreLinkRequest $request): LinkResource
    {
        $this->authorize('create', Link::class);
        $user = $request->user();
        $link = new Link();

        $link->original_url = $request->input('original_url');


        $link->short_token = $request->has('short_token') ?
            $request->input('short_token') :
            $this->generateUniqueShortToken($user->id);

        $link->is_private = $request->input('is_private');
        $link->user_id = $user->id;
        $link->save();

        return new LinkResource($link);
    }

    /**
     * @OA\Get(
     *     path="/api/links/{link}",
     *     summary="Get Link by id",
     *     tags={"Link"},
     *     security={
     *           {"sanctum": {}},
     *     },
     *     @OA\Parameter(
     *          name="link",
     *          in="path",
     *          description="ID of the link",
     *          required=true,
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *     @OA\Response(response=200, description="Successful operation"),
     *     @OA\Response(response=400, description="Invalid request")
     * )
     */
    public function show(Link $link): LinkResource
    {
        $this->authorize('view', $link);
        return new LinkResource($link);
    }


    /**
     * @OA\Patch(
     *     path="/api/links/{link}",
     *     summary="Update Link",
     *     tags={"Link"},
     *     security={
     *           {"sanctum": {}},
     *      },
     *     @OA\Parameter(
     *           name="link",
     *           in="path",
     *           description="ID of the link",
     *           required=true,
     *           @OA\Schema(
     *               type="integer"
     *           )
     *     ),
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(ref="#/components/schemas/UpdateLinkRequest")
     *     ),
     *     @OA\Response(response=200, description="Successful operation"),
     *     @OA\Response(response=400, description="Invalid request")
     * )
     */
    public function update(UpdateLinkRequest $request, Link $link): LinkResource
    {
        $this->authorize('update', $link);
        $link->update($request->all());

        return new LinkResource($link);
    }

    /**
     * @OA\Delete(
     *     path="/api/links/{link}",
     *     summary="Delete Link",
     *     tags={"Link"},
     *     security={
     *           {"sanctum": {}},
     *      },
     *     @OA\Parameter(
     *            name="link",
     *            in="path",
     *            description="ID of the link",
     *            required=true,
     *            @OA\Schema(
     *                type="integer"
     *            )
     *     ),
     *     @OA\Response(response=200, description="Successful operation"),
     *     @OA\Response(response=400, description="Invalid request")
     * )
     */
    public function destroy(Link $link)
    {
        $this->authorize('delete', $link);
        $link->delete();
        return response()->json(['message' => 'Link deleted successfully'], 200);
    }

    public function redirect($token)
    {
        $link = Link::where('short_token', $token)->where('is_private', false)->first();

        if (!$link) {
            return response()->json(['error' => 'Link not found'], 404);
        }

        return redirect()->away($link->original_url);
    }
}
