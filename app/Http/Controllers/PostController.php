<?php

    namespace App\Http\Controllers;
    use App\Models\Post;
    use Illuminate\Http\Request;

    class PostController extends Controller
    {
        public function index(){
            return Post::all();
        }
        public function store(Request $request){
            $validated = $request->validate([
                'name' => 'required|string|max:244',
            ]);
            $post = Post::create([
                'name' => $validated['name'],
                'status' => 'Active',
            ]);
            return response()->json([
                'message' => 'Post created successfully',
                'post' => $post], 201);

        }
        public function update(Request $request,$id){
            $post = Post::findOrFail($id);
            $post->name = $request->name;
            $post->save();
            return response()->json([
                'message' => 'Post updated successfully',
                'post' => $post], 200);
        }
        public function activate($id){
            $post = Post::findOrFail($id);
            $post->status = 'Active';
            $post->save();
            return response()->json([
                'message' => 'Post activated successfully',
                'post' => $post], 200);
        }
        public function deactivate($id){
            $post = Post::findOrFail($id);
            // cannot deactivate if active officer exists
            if($post->officers()->where('status','Active')->count() > 0){
                return response()->json(['error' => 'cannot deactivate post with active officers'], 400);
            }
            $post->status = 'Inactive';
            $post->save();
            return response()->json([
                'message' => 'Post deactivated successfully',
                'post' => $post], 200);
        }
    }
    ?>
