<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Bookshelf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BookController extends Controller
{
    public function index(){
        $data['books'] = Book::with('bookshelf')->get();
        return view('books.index', $data);
    }

    public function create(){
        $data['bookshelves'] = Bookshelf::get();
        return view('books.create', $data);
    }
    public function edit(string $id){
        $data['bookshelves'] = Bookshelf::get();
        $data['book'] = Book::findOrFail($id);
        return view('books.edit', $data);
    }
    

    public function store(Request $request){
        $validated = $request->validate([
            'title' => 'required|max:225',
            'author' => 'required|max:225',
            'year' => 'required|max:2024',
            'publisher' => 'required|max:225',
            'city' => 'required|max:50',
            'cover' => 'required',
            'bookshelf_id' => 'required',
        ]);
        if($request->hasFile('cover')){
            $path = $request->file('cover')->storeAs(
                'public/cover_buku',
                'cover_buku'.time() . '.' . $request->file('cover')->extension()
            );
            $validated['cover']= basename($path);
        }
        Book::create( $validated);

       $notification = array( 
            'message' => 'Data buku berhasil dihapus', 
            'alert-type' => 'success' 
        ); 
        
        if($request->save == true) return redirect()->route('book')->with($notification);
        else return redirect()->route('book.create')->with($notification);
    }

        public function update(Request $request, string $id){
        $book=Book::findOrFail($id);
        $validated = $request->validate([
        'title' => 'required|max:225',
        'author' => 'required|max:225',
        'year' => 'required|max:2024',
        'publisher' => 'required|max:225',
        'city' => 'required|max:50',
        'bookshelf_id' => 'required',
    ]);
    if($request->hasFile('cover')){
        if($book->cover !=null){
            Storage::delete('public/cover_buku'.$request->old_cover);
        }
        $path = $request->file('cover')->storeAs(
            'public/cover_buku',
            'cover_buku'.time() . '.' . $request->file('cover')->extension()
        );
        $validated['cover']= basename($path);
    }
    $book->update($validated);

   $notification = array( 
        'message' => 'Data buku berhasil dihapus', 
        'alert-type' => 'success' 
    ); 
    return redirect()->route('book.create')->with($notification);
}
public function destroy(string $id){
    $book=Book::findOrFail($id);
    Storage::delete('public/cover_buku'.$book->cover);
    $book->delete();
    
    $notification = array( 
    'message' => 'Data buku berhasil dihapus', 
    'alert-type' => 'success' 
); 
return redirect()->route('book.create')->with($notification);
}
}
