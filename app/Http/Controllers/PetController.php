<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Psr\Http\Message\ResponseInterface;

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
            $pets = json_decode($response->getBody()->getContents(), true);
            // Sprawdzamy, czy $pets to tablica, jeśli nie, ustawiamy pustą tablicę
            $pets = is_array($pets) ? $pets : [];
            return view('pets.index', compact('pets'));
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
            'id' => (int) $request->input('id'), // Rzutujemy na int
            'name' => (string) $request->input('name'), // Rzutujemy na string
            'status' => (string) ($request->input('status') ?? 'available') // Domyślna wartość
        ];

        try {
            $response = $this->client->post('pet', [
                'json' => $data,
                'headers' => ['Content-Type' => 'application/json']
            ]);

            if ($response->getStatusCode() === 200) {
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
            $pet = json_decode($response->getBody()->getContents(), true);

            // Sprawdzamy, czy $pet to tablica i ma klucz 'status'
            if (!is_array($pet) || !isset($pet['status'])) {
                return back()->with('error', 'Nieprawidłowe dane zwierzaka!');
            }

            if ($pet['status'] === 'sold') {
                return back()->with('error', 'Nie można edytować sprzedanego zwierzaka!');
            }

            return view('pets.edit', compact('pet'));
        } catch (RequestException $e) {
            return back()->with('error', 'Błąd podczas pobierania zwierzaka: ' . $e->getMessage());
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

            if ($response->getStatusCode() === 200) {
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

            if ($response->getStatusCode() === 200) {
                return redirect()->route('pets.index')->with('success', 'Zwierzak usunięty!');
            }

            return back()->with('error', 'Błąd podczas usuwania zwierzaka!');
        } catch (RequestException $e) {
            return back()->with('error', 'Błąd podczas usuwania: ' . $e->getMessage());
        }
    }
}