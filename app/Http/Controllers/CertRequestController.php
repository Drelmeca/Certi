<?php

namespace App\Http\Controllers;

use App\Models\CertRequest;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
class CertRequestController extends Controller
{
    public function index()
    {
        // If logged-in admin (role == 2) show all requests and guest requests separately
        if (Auth::check() && Auth::user()->role === 2) {
            $all = CertRequest::all();
            $guest = CertRequest::whereNull('user_id')->get();

            return Inertia::render('CertRequest/Index', [
                'CertRequest' => $all,
                'guestRequests' => $guest,
            ]);
        }

        // For normal authenticated users show only their own requests
        if (Auth::check()) {
            $userRequests = CertRequest::where('user_id', Auth::id())->get();
            return Inertia::render('CertRequest/Index', [
                'CertRequest' => $userRequests,
            ]);
        }

        // Fallback: return empty list
        return Inertia::render('CertRequest/Index', [
            'CertRequest' => [],
        ]);
    }
    public function create()
    {
        return inertia('CertRequest/Edit', [
            'person' => null,
            'mode' => 'create',
             'userType' => 'user',
        ]);
    }
    public function store(Request $request)
    {
        // Validate the request data
        $request->validate([
            'first_name' => 'required|string|max:15',
            'middle_name' => 'nullable|string|max:15',
            'last_name' => 'required|string|max:15',
            'suffix' => 'nullable|string|max:15',
            'email' => 'required|email|max:25',
            'contact_number' => 'required|string|max:11',
            'request_type' => 'required|string|max:25',
            'request_purpose' => 'required|string|max:50',
        ]);

        try {
            if ($request->input('id')) {
                // Update existing record
                $CertRequest = CertRequest::findOrFail($request->input('id'));
                $CertRequest->first_name = $request->input('first_name');
                $CertRequest->middle_name = $request->input('middle_name');
                $CertRequest->last_name = $request->input('last_name');
                $CertRequest->suffix = $request->input('suffix');
                $CertRequest->email = $request->input('email');
                $CertRequest->request_type = $request->input('request_type');
                $CertRequest->request_purpose = $request->input('request_purpose');
                $CertRequest->contact_number = $request->input('contact_number');
                $CertRequest->save();
                return response()->json([
                    'success' => true,
                    'message' => 'Data updated successfully.',
                ]);
            } else {
                // Create new record
                $requestCert = new CertRequest();
                // attach user if authenticated, otherwise leave null for guest requests
                if (Auth::check()) {
                    $requestCert->user_id = Auth::id();
                }
                $requestCert->first_name = $request->input('first_name');
                $requestCert->middle_name = $request->input('middle_name');
                $requestCert->last_name = $request->input('last_name');
                $requestCert->suffix = $request->input('suffix');
                $requestCert->email = $request->input('email');
                $requestCert->request_type = $request->input('request_type');
                $requestCert->request_purpose = $request->input('request_purpose');
                $requestCert->contact_number = $request->input('contact_number');
                $requestCert->status = 0;
                $requestCert->save();
                return response()->json([
                    'success' => true,
                    'message' => 'Data saved successfully.',
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }


    public function edit($id)
    {
        $CertRequest = CertRequest::where('id', $id)->first();
        return Inertia::render('CertRequest/Edit', [
            'person' => $CertRequest,
            'mode' => 'edit',
            'userType' => 'user',
        ]);
    }

    public function destroy($id)
    {
        // Delete the record
        // Redirect or return a response
    }

    public function approve($id)
    {
        if (!Auth::check() || Auth::user()->role !== 2) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $cert = CertRequest::findOrFail($id);
        $cert->status = 1; // approved
        $cert->save();

        return response()->json(['success' => true, 'message' => 'Request approved.']);
    }

    public function reject($id)
    {
        if (!Auth::check() || Auth::user()->role !== 2) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $cert = CertRequest::findOrFail($id);
        $cert->status = 2; // rejected
        $cert->save();

        return response()->json(['success' => true, 'message' => 'Request rejected.']);
    }
}
