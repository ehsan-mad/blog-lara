<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $posts = Post::all();

        return view('admin.posts.index', compact('posts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        $categories = Category::all();

        return view('admin.posts.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $request->validate(
            [
                'title'          => 'required|unique:posts,title',
                'content'        => 'required',
                'category_id'    => 'required|exists:categories,id',
                'user_id'        => 'required',
                'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg',
            ],

        );

        //handle image upload
        $imagePath = null;
        if ($request->hasFile('featured_image')) {

            $image     = $request->file('featured_image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $imagePath = 'uploads/' . $imageName;
            $image->move(public_path('uploads'), $imageName);
        }

        //create post
        Post::create([
            'title'          => $request->title,
            'content'        => $request->content,
            'category_id'    => $request->category_id,
            'user_id'        => $request->user_id,
            'featured_image' => $imagePath,
        ]);

        return redirect()->route('posts.index')->with('success', 'Post created successfully!');

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
        $post       = Post::findOrFail($id);
        $categories = Category::all();
        return view('admin.posts.edit', compact('post', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //

        $post = Post::findOrFail($id);

        $request->validate(
            [
                'title'          => 'required|unique:posts,title,' . $post->id,
                'content'        => 'required',
                'category_id'    => 'required|exists:categories,id',
                'user_id'        => 'required',
                'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg',
            ],

        );

        //handle image upload
        $imagePath = null;
        if ($request->hasFile('featured_image')) {

            // delete previous image if exists
            if ($post->featured_image && file_exists(public_path($post->featured_image))) {
                $previousImagePath = public_path($post->featured_image);

                unlink($previousImagePath);

            }
            // upload new image
            $image     = $request->file('featured_image');
            $imageName = time() . '_' . $request->file('featured_image')->getClientOriginalName();
            $imagePath = 'uploads/' . $imageName;

            $image->file('featured_image')->move(public_path('uploads'), $imageName);

        }

        $post->update([
            'title'          => $request->title,
            'content'        => $request->content,
            'category_id'    => $request->category_id,
            'user_id'        => $request->user_id,
            'featured_image' => $imagePath,
        ]);

        return redirect()->route('posts.index')->with('success', 'Post updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
        $post = Post::findOrFail($id);

        // delete previous image if exists
        if ($post->featured_image && file_exists(public_path($post->featured_image))) {
            $previousImagePath = public_path($post->featured_image);

            unlink($previousImagePath);

        }
        $post->delete();
    }
}
