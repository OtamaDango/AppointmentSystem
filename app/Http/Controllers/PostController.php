<?php

    namespace App\Http\Controllers;
    use App\Models\Post;
    use Illuminate\Http\Request;

    class PostController extends Controller
    {
        public function index(){
            $posts = Post::all();
            return view('posts.index', compact('posts'));
        }
        public function create() {
            return view('posts.create');
        }
        public function edit($id){
            $post = Post::findOrFail($id);
            return view('posts.edit', compact('post'));
        }
        public function store(Request $request){
            $validated = $request->validate([
                'name' => 'required|string|max:244',
            ]);
            $post = Post::create([
                'name' => $validated['name'],
                'status' => 'Active',
            ]);
            return redirect()->route('posts.index')->with('success', 'Post created successfully');
        }
        public function update(Request $request,$id){
            $post = Post::findOrFail($id);
            $post->name = $request->name;
            $post->save();
            return redirect()->route('posts.index')->with('success', 'Post updated successfully');
        }
        public function activate($id){
            $post = Post::findOrFail($id);
            $post->status = 'Active';
            $post->save();
            return redirect()->route('posts.index')->with('success', 'Post activated successfully');
        }
        public function deactivate($id){
            $post = Post::findOrFail($id);
            // cannot deactivate if active officer exists
            if($post->officers()->where('status','Active')->count() > 0){
                return response()->json(['error' => 'cannot deactivate post with active officers'], 400);
            }
            $post->status = 'Inactive';
            $post->save();
            return redirect()->route('posts.index')->with('success', 'Post deactivated successfully');
        }
    }
    ?>
