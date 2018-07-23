<?php

namespace App\Http\Controllers;

use App\Group;
use App\ShortenedUrl;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class GenerateUrlController extends Controller
{
    public function __construct()
    {
        $this->middleware('URLMustBelongToOwner')->only([
            'show',
            'edit',
            'update',
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $urls = ShortenedUrl::where('user_id', Auth::id())
            ->paginate(20);

        return view('generate.index', [
            'urls' => $urls,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('generate.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $this->validate($request, [
            'type'             => [
                'required',
                Rule::in(['single', 'group']),
                'bail',
            ],
            'alias'            => [
                'sometimes',
                Rule::unique('shortened_urls'),
            ],
            'mobile_number'    => [
                'required_if:type,single',
                'phone:MY',
            ],
            'mobile_numbers'   => [
                'required_if:type,group',
                'between:2,5',
            ],
            'mobile_numbers.*' => 'distinct|phone:MY',
        ]);

        if ($request->type === 'single') {

            $url = Auth::user()->addURL(new ShortenedUrl(
                $request->only(['alias', 'mobile_number', 'text', 'type'])
            ));

        } elseif ($request->type === 'group') {

            $url = Auth::user()->addURL(new ShortenedUrl(
                $request->only(['alias', 'type', 'text'])
            ));

            foreach ($request->mobile_numbers as $number) {
                $url->group()->create([
                    'mobile_number' => $number,
                ]);
            }

        }

        return redirect()->route('generate.show', $url->hashid);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(ShortenedUrl $url)
    {

        return view('generate.show', [
            'url' => $url,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(ShortenedUrl $url)
    {
        return view('generate.edit', [
            'url' => $url,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ShortenedUrl $url)
    {

        $this->validate($request, [
            'type'             => [
                'required',
                Rule::in(['single', 'group']),
                'bail',
            ],
            'alias'            => [
                'required',
                Rule::unique('shortened_urls')->ignore($url->id),
            ],
            'text'             => 'sometimes|max:5000',
            'mobile_number'    => [
                'required_if:type,single',
                'text' => 'sometimes|max:5000',
                'phone:MY',
            ],
            'mobile_numbers'   => [
                'required_if:type,group',
                'between:2,5',
            ],
            'mobile_numbers.*' => [
                'required_if:type,group',
                'distinct',
                'phone:MY',
            ],
        ]);

        if ($url->type === 'single') {

            $url->update($request->only('alias', 'mobile_number', 'text'));

        } elseif ($url->type === 'group') {

            $mobile_numbers = $request->mobile_numbers;

            $existingNumber = $url->group()->pluck('mobile_number')->toArray();

            $editedNumbers = array_diff($mobile_numbers, $existingNumber);

            $url->update($request->only('alias', 'text'));

            foreach ($mobile_numbers as $number) {
                $url->group()->firstOrCreate(['mobile_number' => $number]);
            }

            foreach ($editedNumbers as $number) {
                $url->group()->where('mobile_number', $number)->delete();
            }

        }

        return redirect()->route('generate.show', $url->hashid);
    }
}
