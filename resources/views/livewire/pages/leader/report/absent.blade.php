<x-report-layout>
@php
    $attendance = function ($participant_id, $reception, $program, $level) {
    $absent = \App\Models\Absent::select('reception_id', 'program_id', 'level')
        ->selectSub(function ($query) use ($participant_id) {
            $query->from('absent_details')
                ->selectRaw('COUNT(*)')
                ->where('participant_id', $participant_id)
                ->where('status', 'present')
                ->groupBy('participant_id');
        }, 'absent_count')
        ->where('reception_id', $reception)
        ->where('program_id', (int)$program)
        ->where('level', (int)$level)
        ->groupBy('reception_id', 'program_id', 'level')
        ->first();
    return $absent->absent_count;
};

$meeting = function ($program_id, $reception) {
    return \App\Models\Opening::where('program_id', $program_id)->where('reception_id', $reception)->first()->meeting ?? 0;
}
@endphp
    <table style="width: 100%; border-collapse: collapse; padding: 20px;">
        <tr>
            <!-- Kolom Logo -->
            <td style="width: 20%; text-align: left; vertical-align: middle;">
                <img src="{{ public_path() . '/img/logo_white.svg' }}" alt="Logo Kiri"
                     style="width: 170px; height: auto;">
            </td>
            <!-- Kolom Konten -->
            <td style="width: 80%; text-align: center; vertical-align: middle;">
                <h1 style="font-size: 32px; margin: 0;">Laporan Absensi</h1>
                <h3 style="font-size: 20px; margin: 10px 0;">
                    Absen Peserta periode {{ \Carbon\Carbon::parse($reception->start_course)->locale('id')->isoFormat('DD-MM-YYYY') }} s/d {{ \Carbon\Carbon::parse($reception->complete_course)->locale('id')->isoFormat('DD-MM-YYYY') }}
                </h3>
                <p style="font-size: 14px; margin: 0;">Laporan ini menyajikan detail pembayaran peserta selama periode yang disebutkan.</p>
            </td>
            <td style="width: 20%; text-align: left; vertical-align: middle;">
               &nbsp;
            </td>
        </tr>
    </table>

    <div style="background-color: #ffffff; padding: 16px;">
        <div style="overflow-x: auto; border: 1px solid #d1d5db; ">
            <table style="width: 100%; font-size: 0.875rem; text-align: left; color: #6b7280; border-collapse: collapse;">
                <thead style="font-size: 0.75rem; text-transform: uppercase; background-color: #f9fafb; color: #374151;">
                <tr style="color: #4b5563;">
                    <th style="padding: 0.75rem;">#</th>
                    <th style="padding: 0.75rem;">Nama</th>
                    <th style="padding: 0.75rem;">Phone</th>
                    <th style="padding: 0.75rem;">Alamat</th>
                    <th style="padding: 0.75rem;">Kelas</th>
                    <th style="padding: 0.75rem;">Total Kehadiran</th>
                    <th style="padding: 0.75rem;">Pertemuan</th>
                </tr>
                </thead>
                <tbody>
                @if(count($participants) > 0)
                    @foreach($participants as $key => $participant)
                        <tr style="background-color: #ffffff; border-bottom: 1px solid #e5e7eb;">
                            <td style="padding: 0.75rem; border-bottom: 1px solid #e5e7eb;">
                                {{ $loop->iteration }}
                            </td>
                            <th scope="row" style="display: flex; align-items: center; padding: 0.75rem; color: #1f2937; white-space: nowrap;">
                                <img style="width: 2.5rem; height: 2.5rem; border-radius: 9999px; margin-right: 0.75rem;" src="{{ public_path('storage/' . $participant->user->profile_photo_path) }}" alt="">
                                <div>
                                    <div style="font-weight: 600;">{{ $participant->user->personal_data->full_name }}</div>
                                    <div style="font-size: 0.875rem; color: #6b7280;">{{ $participant->user->email }}</div>
                                </div>
                            </th>
                            <td style="padding: 0.75rem; text-align: center;">
                                {{ $participant->user->personal_data->phone }}
                            </td>
                            <th scope="row" style="padding: 0.75rem; color: #1f2937;text-align: center;">
                                <div>
                                    <div style="font-weight: 600;">{{ $participant->user->personal_data->address }}</div>
                                    <div style="font-size: 0.875rem; color: #6b7280;">
                                        RT {{ $participant->user->personal_data->rt }} : RW {{ $participant->user->personal_data->rw }}</div>
                                </div>
                            </th>
                            <td style="padding: 0.75rem; white-space: nowrap; text-align: center;">
                                {{ $participant->program->name }}
                            </td>
                            <td style="padding: 0.75rem; white-space: nowrap; text-align: center;">
                                {{ $attendance($participant->id, $reception->id, $program, $level) }} x Hadir
                            </td>
                            <td style="padding: 0.75rem; white-space: nowrap;">
                                {{ $meeting($participant->program->id, $reception->id) }} Pertemuan
                            </td>
                        </tr>
                    @endforeach
                @else
                    <tr style="background-color: #ffffff; border-bottom: 1px solid #e5e7eb;">
                        <td style="padding: 0.75rem; text-align: center;" colspan="8">
                            Tidak ada data
                        </td>
                    </tr>
                @endif
                </tbody>
            </table>
        </div>


    </div>
</x-report-layout>
