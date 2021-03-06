<?php namespace App\DCC\External;

use App\CustomerSpec;
use App\DCC\SpecificationGateway;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ExternalSpecFile implements SpecificationGateway {

    private $spec;
    private $request;

    public function __construct(Request $request, CustomerSpec $spec=null) {
        $this->spec = $spec;
        $this->request = $request;
    }

    function persist() {
        $path = $this->generatePathName();
        $document_name = $this->generateDocumentName();
        $path = $this->request->document->storeAs($path, $document_name);

        $this->getSpecInstance()->update(['document' => $path]);
    }

    function update() {
        $this->persist();
    }

    private function generatePathName() {
        $year = Carbon::now()->year;
        $spec_name = $this->spec->spec_no;

        return "{$year}/{$spec_name}";
    }

    private function generateDocumentName() {

        $name =  [
            'spec_no' => $this->spec->spec_no,
            'spec_revision' => preg_replace("/[^a-z|^0-9|^A-Z]/", "-", $this->getRevision())
        ];
        $implode_name = Str::upper(implode("_", $name));
        $extension = $this->request->document->getClientOriginalExtension();

        return "{$implode_name}.{$extension}";
    }

    private function getSpecInstance() {

        return $this->spec->customerSpecRevision()
            ->whereCustomerSpecId($this->spec->id)
            ->whereRevision($this->request->revision);
    }
    
    private function getRevision() {
        return $this->getSpecInstance()->first()->revision;
    }
}