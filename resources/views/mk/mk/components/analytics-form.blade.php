<div class="section">
  <div class="section-header">
    <h3 class="section-title">Analytics Filter</h3>
  </div>

  <div class="section-body">
    <form method="GET" action="{{ $action ?? request()->url() }}" style="display: flex; flex-direction: column; gap: 20px;">
      
      <!-- Project Selection -->
      <div style="display: flex; flex-direction: column; gap: 8px;">
        <label style="font-size: 12px; font-weight: 800; color: var(--dark-teal); text-transform: uppercase; letter-spacing: 0.5px;">
          Select Project
        </label>
        <select 
          name="project_id" 
          style="width: 100%; padding: 12px 16px; border: 2px solid var(--beige); border-radius: 12px; background: var(--white); font-family: 'Montserrat', sans-serif; font-size: 14px; font-weight: 600; color: var(--dark-teal); outline: none;">
          @foreach($projects ?? [] as $p)
            @php
              $pid = $p['id'] ?? '';
              $pt = $p['title'] ?? $pid;
            @endphp
            <option value="{{ $pid }}" @selected($pid == ($projectId ?? ''))>
              #{{ $pid }} - {{ $pt }}
            </option>
          @endforeach
        </select>
      </div>

      <!-- Date Range -->
      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
        <div style="display: flex; flex-direction: column; gap: 8px;">
          <label style="font-size: 12px; font-weight: 800; color: var(--dark-teal); text-transform: uppercase; letter-spacing: 0.5px;">
            Start Date
          </label>
          <input 
            type="date" 
            name="start_date" 
            value="{{ $params['startDate'] ?? now()->subDay()->toDateString() }}"
            style="width: 100%; padding: 12px 16px; border: 2px solid var(--beige); border-radius: 12px; background: var(--white); font-family: 'Montserrat', sans-serif; font-size: 14px; font-weight: 600; color: var(--dark-teal); outline: none;">
        </div>

        <div style="display: flex; flex-direction: column; gap: 8px;">
          <label style="font-size: 12px; font-weight: 800; color: var(--dark-teal); text-transform: uppercase; letter-spacing: 0.5px;">
            End Date
          </label>
          <input 
            type="date" 
            name="end_date" 
            value="{{ $params['endDate'] ?? now()->toDateString() }}"
            style="width: 100%; padding: 12px 16px; border: 2px solid var(--beige); border-radius: 12px; background: var(--white); font-family: 'Montserrat', sans-serif; font-size: 14px; font-weight: 600; color: var(--dark-teal); outline: none;">
        </div>
      </div>

      <!-- Time Range -->
      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
        <div style="display: flex; flex-direction: column; gap: 8px;">
          <label style="font-size: 12px; font-weight: 800; color: var(--dark-teal); text-transform: uppercase; letter-spacing: 0.5px;">
            Start Hour
          </label>
          <input 
            type="number" 
            min="0" 
            max="23" 
            name="start_time" 
            value="{{ $params['startTime'] ?? 0 }}"
            style="width: 100%; padding: 12px 16px; border: 2px solid var(--beige); border-radius: 12px; background: var(--white); font-family: 'Montserrat', sans-serif; font-size: 14px; font-weight: 600; color: var(--dark-teal); outline: none;">
        </div>

        <div style="display: flex; flex-direction: column; gap: 8px;">
          <label style="font-size: 12px; font-weight: 800; color: var(--dark-teal); text-transform: uppercase; letter-spacing: 0.5px;">
            End Hour
          </label>
          <input 
            type="number" 
            min="0" 
            max="23" 
            name="end_time" 
            value="{{ $params['endTime'] ?? 23 }}"
            style="width: 100%; padding: 12px 16px; border: 2px solid var(--beige); border-radius: 12px; background: var(--white); font-family: 'Montserrat', sans-serif; font-size: 14px; font-weight: 600; color: var(--dark-teal); outline: none;">
        </div>
      </div>

      <!-- Media Platform -->
      @if($showMedia ?? true)
      <div style="display: flex; flex-direction: column; gap: 8px;">
        <label style="font-size: 12px; font-weight: 800; color: var(--dark-teal); text-transform: uppercase; letter-spacing: 0.5px;">
          Media Platform
        </label>
        <select 
          name="media"
          style="width: 100%; padding: 12px 16px; border: 2px solid var(--beige); border-radius: 12px; background: var(--white); font-family: 'Montserrat', sans-serif; font-size: 14px; font-weight: 600; color: var(--dark-teal); outline: none;">
          <option value="twit" @selected(($params['media'] ?? 'twit') == 'twit')>Twitter</option>
          <option value="fb" @selected(($params['media'] ?? '') == 'fb')>Facebook</option>
          <option value="instagram" @selected(($params['media'] ?? '') == 'instagram')>Instagram</option>
          <option value="youtube" @selected(($params['media'] ?? '') == 'youtube')>YouTube</option>
          <option value="tiktok" @selected(($params['media'] ?? '') == 'tiktok')>TikTok</option>
          <option value="doc" @selected(($params['media'] ?? '') == 'doc')>Document</option>
        </select>
      </div>
      @endif

      <!-- Sentiment Filter -->
      @if($showSentiment ?? false)
      <div style="display: flex; flex-direction: column; gap: 8px;">
        <label style="font-size: 12px; font-weight: 800; color: var(--dark-teal); text-transform: uppercase; letter-spacing: 0.5px;">
          Sentiment
        </label>
        <select 
          name="sentiment"
          style="width: 100%; padding: 12px 16px; border: 2px solid var(--beige); border-radius: 12px; background: var(--white); font-family: 'Montserrat', sans-serif; font-size: 14px; font-weight: 600; color: var(--dark-teal); outline: none;">
          <option value="1" @selected(($params['sentiment'] ?? 1) == 1)>Positive</option>
          <option value="0" @selected(($params['sentiment'] ?? 1) == 0)>Neutral</option>
          <option value="-1" @selected(($params['sentiment'] ?? 1) == -1)>Negative</option>
        </select>
      </div>
      @endif

      <!-- Submit Button -->
      <button 
        type="submit"
        style="width: 100%; padding: 16px; background: var(--brown); color: var(--white); border: none; border-radius: 12px; font-family: 'Montserrat', sans-serif; font-size: 15px; font-weight: 800; cursor: pointer; transition: all 0.2s;"
        onmouseover="this.style.background='var(--dark-teal)'; this.style.transform='translateY(-2px)'"
        onmouseout="this.style.background='var(--brown)'; this.style.transform='translateY(0)'">
        Load Analytics Data
      </button>

      @if($helperText ?? false)
      <div style="font-size: 12px; color: var(--sage); font-weight: 600; text-align: center;">
        {{ $helperText }}
      </div>
      @endif
    </form>
  </div>
</div>