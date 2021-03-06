<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Predictions;
use App\Http\Services\PythonService;
use File;
use Response;


class PredictionsController extends Controller
{

    private $pythonService;
    
    public function __construct() { 
        
        $this->pythonService = new PythonService();
    }

    /**
     * Display a listing of the resource.php artisan route:list
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        
    }   

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'sigla' => 'required',
            'nombre' => 'required'
        ]);
        $pr = Predictions::create($request->all());
        return response()->json([
            'message' => 'Registro insertado con éxito',
            'pr' => $pr
        ]);
        //return "referencia insertada con éxito en la base de datos";
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

        if($id=="list"){

            $empresas = Predictions::all();
            header('Access-Control-Allow-Origin: *');
            return $empresas;

        }
        else if(strpos($id, '.txt') == true){

            $filename = resource_path() . '/python/images/' . $id;
    
            if(!File::exists($filename)) {
    
                return response()->json(['message' => 'txt not found.'], 404);
                
            }else{
    
                $content = File::get($filename);

                
                header("Content-Type: plain/text");
                header('Access-Control-Allow-Origin: *');
                
                $this->utf8_encode_deep($content);
                return response()->json([
                    'message' => $content
                ]);
            }
        }
        else if(strpos($id, '.png') == true){

            $filename = resource_path() . '/python/images/' . $id;

            if(!File::exists($filename)) {
                return response()->json(['message' => 'Image not found.'], 404);
                
            }else{
    
                $handle = fopen($filename, "rb");
                $contents = fread($handle, filesize($filename));
                fclose($handle);
     
                header("content-type: image/png");
                header('Access-Control-Allow-Origin: *');
                echo $contents;
            }
        }
        else if(!strpos($id, '.png') || !strpos($id, '.txt')){
            header('Access-Control-Allow-Origin: *');
            $this->pythonService->main($id);
        }
/*
        if(!strpos($id, '.png') || !strpos($id, '.txt')){
            header('Access-Control-Allow-Origin: *');
            $this->pythonService->main($id);
        }
       */
    }


    public function utf8_encode_deep(&$input) {
        if (is_string($input)) {
            $input = utf8_encode($input);
        } else if (is_array($input)) {
            foreach ($input as &$value) {
                self::utf8_encode_deep($value);
            }
    
            unset($value);
        } else if (is_object($input)) {
            $vars = array_keys(get_object_vars($input));
    
            foreach ($vars as $var) {
                self::utf8_encode_deep($input->$var);
            }
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
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
    
    }
}
