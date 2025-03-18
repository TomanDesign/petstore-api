<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class PetController extends Controller
{
    private $client;
    private $baseUri = 'https://petstore.swagger.io/v2/';

    public function __construct()
    {
        $this->client = new Client(['base_uri' => $this->baseUri]);
    }

    // Pet's list
    public function index()
    {
        try {
            $response = $this->client->get('pet/findByStatus?status=available');
            $pets = json_decode($response->getBody(), true);
            return view('pets.index', compact('pets'));
        } catch (RequestException $e) {
            return back()->with('error', 'Błąd podczas pobierania danych: ' . $e->getMessage());
        }
    }

    // Add new pet form
    public function create()
    {
        return view('pets.create');
    }

    // Add pet
    public function store(Request $request)
    {
        $data = [
            'id' => $request->id,
            'name' => $request->name,
            'status' => $request->status ?? 'available'
        ];

        try {
            $response = $this->client->post('pet', [
                'json' => $data,
                'headers' => ['Content-Type' => 'application/json']
            ]);

            if($response->getStatusCode() == 200) {
                return redirect()->route('pets.index')->with('success', 'Zwierzak dodany!');
            } else {
                return back()->with('error', 'Błąd podczas dodawania zwierzaka!');
            }

        } catch (RequestException $e) {
            return back()->with('error', 'Błąd podczas dodawania: ' . $e->getMessage());
        }
    }

    // Pet's edit form
    public function edit($id)
    {
        try {
            $response = $this->client->get("pet/{$id}");
            $pet = json_decode($response->getBody(), true);

            if($pet['status'] == 'sold') {
                return back()->with('error', 'Nie można edytować sprzedanego zwierzaka!');
            } else {
                return view('pets.edit', compact('pet'));
            }

        } catch (RequestException $e) {
            return back()->with('error', 'Błąd podczas pobierania zwierzaka: ' . $e->getMessage());
        }
    }

    // Pet's update
    public function update(Request $request, $id)
    {
        $data = [
            'id' => $id,
            'name' => $request->name,
            'status' => $request->status ?? 'available'
        ];

        try {
            $response = $this->client->put("pet", [
                'json' => $data,
                'headers' => ['Content-Type' => 'application/json']
            ]);

            if($response->getStatusCode() == 200) {
                return redirect()->route('pets.index')->with('success', 'Zwierzak zaktualizowany!');
            } else {
                return back()->with('error', 'Błąd podczas aktualizacji zwierzaka!');
            }

        } catch (RequestException $e) {
            return back()->with('error', 'Błąd podczas aktualizacji: ' . $e->getMessage());
        }
    }

    // Delete pet
    public function destroy($id)
    {
        try {
            $response = $this->client->delete("pet/{$id}");

            if($response->getStatusCode() == 200) {
                return redirect()->route('pets.index')->with('success', 'Zwierzak usunięty!');
            } else {
                return back()->with('error', 'Błąd podczas usuwania zwierzaka!');
            }

        } catch (RequestException $e) {
            return back()->with('error', 'Błąd podczas usuwania: ' . $e->getMessage());
        }
    }
}