<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class PetController extends Controller
{
    private Client $client;
    private string $baseUri = 'https://petstore.swagger.io/v2/';

    public function __construct()
    {
        $this->client = new Client(['base_uri' => $this->baseUri]);
    }

    // Pet's list
    public function index(): View|RedirectResponse
    {
        try {
            $response = $this->client->get('pet/findByStatus?status=available');
            $responseCode = $response->getStatusCode();

            switch($responseCode){
                case 400:
                    return back()->with('error', 'Niepoprawny status!');
                case 200:
                    $pets = json_decode($response->getBody()->getContents(), true);

                    // Chack if $pets is an array, if not. set empty array
                    $pets = is_array($pets) ? $pets : [];

                    return view('pets.index', compact('pets'));
            }


        } catch (RequestException $e) {
            return back()->with('error', 'Błąd podczas pobierania danych: ' . $e->getMessage());
        }
    }

    // Add new pet form
    public function create(): View
    {
        return view('pets.create');
    }

    // Add pet
    public function store(Request $request): RedirectResponse
    {
        $data = [
            'id' => (int) $request->input('id'),
            'name' => (string) $request->input('name'),
            'status' => (string) ($request->input('status') ?? 'available')
        ];

        try {
            $response = $this->client->post('pet', [
                'json' => $data,
                'headers' => ['Content-Type' => 'application/json']
            ]);
            $responseCode = $response->getStatusCode();

            switch($responseCode){
                case 405:
                    return back()->with('error', 'Niepoprawne dane zwierzaka!');
                case 200:
                    return redirect()->route('pets.index')->with('success', 'Zwierzak dodany!');
            }

            return back()->with('error', 'Błąd podczas dodawania zwierzaka!');

        } catch (RequestException $e) {
            return back()->with('error', 'Błąd podczas dodawania: ' . $e->getMessage());
        }
    }

    // Pet's edit form
    public function edit(int $id): View|RedirectResponse
    {
        try {

            $response = $this->client->get("pet/{$id}");
            $responseCode = $response->getStatusCode();

            switch($responseCode){
                case 400:
                    return back()->with('error', 'Niepoprawny kod zwierzaka!');
                case 404:
                    return back()->with('error', 'Nie znaleziono zwierzaka!');
                case 200:
                    $pet = json_decode($response->getBody()->getContents(), true);

                    if (!is_array($pet) || !isset($pet['status'])) {
                        return back()->with('error', 'Nieprawidłowe dane zwierzaka!');
                    }

                    if ($pet['status'] === 'sold') {
                        return back()->with('error', 'Nie można edytować sprzedanego zwierzaka!');
                    }

                    return view('pets.edit', compact('pet'));
            }

        } catch (RequestException $e) {
            return back()->with('error', 'Błąd podczas pobierania zwierzaka' . $e->getMessage());
        }
    }

    // Pet's update
    public function update(Request $request, int $id): RedirectResponse
    {
        $data = [
            'id' => $id,
            'name' => (string) $request->input('name'),
            'status' => (string) ($request->input('status') ?? 'available')
        ];

        try {
            $response = $this->client->put("pet", [
                'json' => $data,
                'headers' => ['Content-Type' => 'application/json']
            ]);
            $responseCode = $response->getStatusCode();

            switch($responseCode){
                case 400:
                    return back()->with('error', 'Niepoprawny identyfikator zwierzaka!');
                case 404:
                    return back()->with('error', 'Nie znaleziono zwierzaka!');
                case 200:
                    return redirect()->route('pets.index')->with('success', 'Zwierzak zaktualizowany!');
            }

            return back()->with('error', 'Błąd podczas aktualizacji zwierzaka!');

        } catch (RequestException $e) {
            return back()->with('error', 'Błąd podczas aktualizacji: ' . $e->getMessage());
        }
    }

    // Delete pet
    public function destroy(int $id): RedirectResponse
    {
        try {
            $response = $this->client->delete("pet/{$id}");
            $responseCode = $response->getStatusCode();

            switch($responseCode){
                case 400:
                    return back()->with('error', 'Niepoprawny kod zwierzaka!');
                case 404:
                    return back()->with('error', 'Nie znaleziono zwierzaka!');
                case 200:
                    return redirect()->route('pets.index')->with('success', 'Zwierzak usunięty!');
            }

            return back()->with('error', 'Błąd podczas usuwania zwierzaka!');

        } catch (RequestException $e) {
            return back()->with('error', 'Błąd podczas usuwania: ' . $e->getMessage());
        }
    }
}