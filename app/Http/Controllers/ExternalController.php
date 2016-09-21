<?php namespace App\Http\Controllers;

use App\CustomerSpec;
use App\CustomerSpecCategory;
use App\DCC\File\Document;
use App\Http\Requests\ExternalSpecRequest;
use Illuminate\Http\Request;
use JavaScript;

class ExternalController extends Controller {

    public function index() {
        $categories = CustomerSpecCategory::getCategoryList();
        JavaScript::put([
            'customer_name' => $categories->first()->customer_name,
        ]);

        return view('external.index', [
            "categories" => $categories
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        return view('external.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param ExternalSpecRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(ExternalSpecRequest $request) {
        return $request;
    }

    /**
     * Display the specified resource.
     *
     * @param CustomerSpec $external
     * @return \Illuminate\Http\Response
     * @internal param int $id
     */
    public function show(CustomerSpec $external) {
        $document = new Document($external->customerSpecRevision()->orderBy('revision','desc')->first()->document);
        return $document->showPDF();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(CustomerSpec $external) {
        return view("external.edit", ['spec' => $external]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ExternalSpecRequest $request, CustomerSpec $external)
    {

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}